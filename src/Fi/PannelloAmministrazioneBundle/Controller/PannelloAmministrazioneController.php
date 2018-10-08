<?php

namespace Fi\PannelloAmministrazioneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Fi\OsBundle\DependencyInjection\OsFunctions;
use Fi\PannelloAmministrazioneBundle\DependencyInjection\PannelloAmministrazioneUtils;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\FlockStore;

class PannelloAmministrazioneController extends Controller
{

    protected $apppaths;
    protected $locksystem;
    protected $factory;

    public function __construct()
    {
        $store = new FlockStore(sys_get_temp_dir());
        $factory = new Factory($store);
        $this->locksystem = $factory->createLock('pannelloamministrazione-command');
        $this->locksystem->release();
    }

    public function indexAction()
    {
        $finder = new Finder();
        $fs = new Filesystem();
        $this->apppaths = $this->get("pannelloamministrazione.projectpath");

        $projectDir = $this->apppaths->getRootPath();
        $bundlelists = $this->getParameter('kernel.bundles');
        $bundles = array();
        foreach ($bundlelists as $bundle) {
            if (substr($bundle, 0, 2) === 'Fi') {
                $bundle = str_replace('\\', '/', $bundle);
                $bundlepath = $this->apppaths->getSrcPath() . DIRECTORY_SEPARATOR . substr($bundle, 0, strripos($bundle, '/'));
                if ($fs->exists($bundlepath)) {
                    $bundles[] = substr($bundle, 0, strripos($bundle, '/'));
                }
            }
        }
        $docDir = $this->apppaths->getDocPath();
        $themes = array("sunny", "redmond", "cupertino", "blitzer", "lightness", "humanity",
            "eggplant", "excitebyke", "flick", "images", "peppergrinder", "overcast", "lefrog", "southstreet", "start");

        $mwbs = array();

        if ($fs->exists($docDir)) {
            $finder->in($docDir)->files()->name('*.mwb');
            foreach ($finder as $file) {
                $mwbs[] = $file->getBasename();
            }
        }

        if ($fs->exists($projectDir . '/.svn')) {
            $svn = true;
        } else {
            $svn = false;
        }

        if ($fs->exists($projectDir . '/.git')) {
            $git = true;
        } else {
            $git = false;
        }

        if (!OsFunctions::isWindows()) {
            $delcmd = 'rm -rf';
            $setfilelock = "touch " . $this->getParameter("maintenanceLockFilePath");
            $remfilelock = "rm " . $this->getParameter("maintenanceLockFilePath");
            $windows = false;
        } else {
            $delcmd = 'del';
            $setfilelock = 'echo $null >> ' . $this->getParameter("maintenanceLockFilePath");
            $remfilelock = "del " . $this->getParameter("maintenanceLockFilePath");
            $windows = true;
        }

        $dellockfile = "DELETELOCK";
        $delcomposerfile = $delcmd . ' ' . $projectDir . DIRECTORY_SEPARATOR . 'composer.lock';
        $dellogsfiles = $delcmd . ' ' . $this->apppaths->getLogsPath() . DIRECTORY_SEPARATOR . '*';
        $delcacheprodfiles = $delcmd . ' ' . $this->apppaths->getCachePath() . DIRECTORY_SEPARATOR . 'prod' . DIRECTORY_SEPARATOR . '*';
        $delcachedevfiles = $delcmd . ' ' . $this->apppaths->getCachePath() . DIRECTORY_SEPARATOR . 'dev' . DIRECTORY_SEPARATOR . '*';
        $setmaintenancefile = $setfilelock;
        $remmaintenancefile = $remfilelock;

        $comandishell = array(
            'lockfile' => $dellockfile,
            'composerlock' => $this->fixSlash($delcomposerfile),
            'logsfiles' => $this->fixSlash($dellogsfiles),
            'cacheprodfiles' => $this->fixSlash($delcacheprodfiles),
            'cachedevfiles' => $this->fixSlash($delcachedevfiles),
            'setmaintenancefile' => $setmaintenancefile,
            'remmaintenancefile' => $remmaintenancefile,
        );

        $twigparms = array('svn' => $svn, 'git' => $git, 'bundles' => $bundles, 'mwbs' => $mwbs,
            'rootdir' => $this->fixSlash($projectDir),
            'comandishell' => $comandishell, 'iswindows' => $windows,
            'themes' => $themes);

        return $this->render('PannelloAmministrazioneBundle:PannelloAmministrazione:index.html.twig', $twigparms);
    }

    private function fixSlash($path)
    {
        return str_replace('\\', '\\\\', $path);
    }

    private function getLockMessage()
    {
        return "<h2 style='color: orange;'>E' già in esecuzione un comando, riprova tra qualche secondo!</h2>";
    }

    public function aggiornaSchemaDatabaseAction()
    {
        if (!$this->locksystem->acquire()) {
            return new Response($this->getLockMessage());
        } else {
            $this->locksystem->acquire();
            $command = $this->get("pannelloamministrazione.commands");
            $result = $command->aggiornaSchemaDatabase();

            $this->locksystem->release();
            $twigparms = array('errcode' => $result['errcode'], 'command' => $result['command'], 'message' => $result['message']);

            return $this->render('PannelloAmministrazioneBundle:PannelloAmministrazione:outputcommand.html.twig', $twigparms);
        }
    }

    /* FORMS */

    public function generateFormCrudAction(Request $request)
    {
        if (!$this->locksystem->acquire()) {
            return new Response($this->getLockMessage());
        } else {
            $entityform = $request->get('entityform');

            $this->locksystem->acquire();

            $command = $this->get("pannelloamministrazione.commands");
            $ret = $command->generateFormCrud($entityform);

            $this->locksystem->release();
            //$retcc = '';
            if ($ret['errcode'] < 0) {
                return new Response($ret['message']);
            } else {
                //$retcc = $command->clearCacheEnv($this->get('kernel')->getEnvironment());
            }
            $twigparms = array('errcode' => $ret['errcode'], 'command' => $ret['command'], 'message' => $ret['message']);

            return $this->render('PannelloAmministrazioneBundle:PannelloAmministrazione:outputcommand.html.twig', $twigparms);
        }
    }

    /* ENTITIES */

    public function generateEntityAction(Request $request)
    {
        if (!$this->locksystem->acquire()) {
            return new Response($this->getLockMessage());
        } else {
            $this->locksystem->acquire();
            $wbFile = $request->get('file');
            $command = $this->get("pannelloamministrazione.commands");
            $ret = $command->generateEntity($wbFile);
            $this->locksystem->release();
            return new Response($ret['message']);
        }
    }

    /* ENTITIES */

    public function generateEntityClassAction(Request $request)
    {
        if (!$this->locksystem->acquire()) {
            return new Response($this->getLockMessage());
        } else {
            $this->locksystem->acquire();
            $command = $this->get("pannelloamministrazione.commands");
            $ret = $command->generateEntityClass();
            $this->locksystem->release();

            return new Response($ret['message']);
        }
    }

    /* VCS (GIT,SVN) */

    public function getVcsAction()
    {
        set_time_limit(0);
        $this->apppaths = $this->get("pannelloamministrazione.projectpath");
        if (!$this->locksystem->acquire()) {
            return new Response($this->getLockMessage());
        } else {
            $this->locksystem->acquire();
            $command = $this->get("pannelloamministrazione.commands");
            $result = $command->getVcs();
            $this->locksystem->release();
            if ($result['errcode'] < 0) {
                $responseout = '<pre>Errore nel comando: <i style = "color: white;">' . $result['command'] . '</i>'
                        . '<br/><i style = "color: red;">' . nl2br($result['errmsg']) . '</i></pre>';
            } else {
                $responseout = '<pre>Eseguito comando: <i style = "color: white;">' . $result['command'] . '</i><br/>' .
                        nl2br($result['errmsg']) . '</pre>';
            }

            return new Response($responseout);
        }
    }

    /* CLEAR CACHE */

    /**
     * Suppress PMD warnings per exit.
     *
     * @//SuppressWarnings(PHPMD)
     */
    public function clearCacheAction(Request $request)
    {
        set_time_limit(0);
        if (!$this->locksystem->acquire()) {
            return new Response($this->getLockMessage());
        } else {
            $this->locksystem->acquire();
            $command = $this->get("pannelloamministrazione.commands");
            $result = $command->clearcache();

            $this->locksystem->release();

            /* Uso exit perchè new response avendo cancellato la cache schianta non avendo più a disposizione i file */
            //return $commanddev . '<br/>' . $cmdoutputdev . '<br/><br/>' . $commandprod . '<br/>' . $cmdoutputprod;
            return new Response(nl2br($result));
            //exit(nl2br($result));
            //return dump($result);
        }
    }

    /* CLEAR CACHE */

    public function symfonyCommandAction(Request $request)
    {
        set_time_limit(0);
        $comando = $request->get('symfonycommand');
        if (!$this->locksystem->acquire()) {
            return new Response($this->getLockMessage());
        } else {
            $this->locksystem->acquire();
            $this->apppaths = $this->get("pannelloamministrazione.projectpath");
            $pammutils = new PannelloAmministrazioneUtils($this->container);
            $phpPath = OsFunctions::getPHPExecutableFromPath();
            $result = $pammutils->runCommand($phpPath . ' ' . $this->apppaths->getConsole() . ' ' . $comando);

            $this->locksystem->release();
            if ($result['errcode'] < 0) {
                $responseout = 'Errore nel comando: <i style = "color: white;">' .
                        str_replace(';', '<br/>', str_replace('&&', '<br/>', $comando)) .
                        '</i><br/><i style = "color: red;">' . nl2br($result['errmsg']) . '</i>';

                return new Response($responseout);
            }
            $responseout = '<pre>Eseguito comando:<br/><br/><i style = "color: white;">' .
                    str_replace(';', '<br/>', str_replace('&&', '<br/>', $comando)) . '</i><br/><br/>' .
                    str_replace("\n", '<br/>', $result['errmsg']) . '</pre>';

            return new Response($responseout);
        }
    }

    /**
     * Suppress PMD warnings per exit.
     *
     * @SuppressWarnings(PHPMD)
     */
    public function unixCommandAction(Request $request)
    {
        set_time_limit(0);
        $pammutils = new PannelloAmministrazioneUtils($this->container);
        $command = $request->get('unixcommand');
        //Se viene lanciato il comando per cancellare il file di lock su bypassa tutto e si lancia
        $dellockfile = "DELETELOCK";
        if ($command == $dellockfile) {
            $this->locksystem->release();
            return new Response('File di lock cancellato');
        }

        if (!$this->locksystem->acquire()) {
            return new Response($this->getLockMessage());
        } else {
            $this->locksystem->acquire();
            $result = $pammutils->runCommand($command);

            $this->locksystem->release();
            // eseguito deopo la fine del comando
            if ($result['errcode'] < 0) {
                $errmsg = 'Errore nel comando: <i style = "color: white;">' .
                        str_replace(';', '<br/>', str_replace('&&', '<br/>', $command)) .
                        '</i><br/><i style = "color: red;">' . nl2br($result['errmsg']) . '</i>';

                return new Response($errmsg);
                //exit(nl2br($errmsg));
                //Uso exit perchè new response avendo cancellato la cache schianta non avendo più a disposizione i file
                //return;
                /* return new Response('Errore nel comando: <i style = "color: white;">' .
                 * $command . '</i><br/><i style = "color: red;">' . str_replace("\n", '<br/>', $process->getErrorOutput()) . '</i>'); */
            }
            $msgok = '<pre>Eseguito comando:<br/><i style = "color: white;"><br/>' .
                    str_replace(';', '<br/>', str_replace('&&', '<br/>', $command)) . '</i><br/>' .
                    nl2br($result['errmsg']) . '</pre>';
            //Uso exit perchè new response avendo cancellato la cache schianta non avendo più a disposizione i file
            return new Response($msgok);
            //exit(nl2br($msgok));
            //return;
            /* return new Response('<pre>Eseguito comando: <i style = "color: white;">' . $command .
             * '</i><br/>' . str_replace("\n", "<br/>", $process->getOutput()) . "</pre>"); */
        }
    }

    public function phpunittestAction(Request $request)
    {
        set_time_limit(0);
        $this->apppaths = $this->get("pannelloamministrazione.projectpath");
        if (!$this->locksystem->acquire()) {
            return new Response($this->getLockMessage());
        } else {
            if (!OsFunctions::isWindows()) {
                $this->locksystem->acquire();
                //$phpPath = OsFunctions::getPHPExecutableFromPath();
                $sepchr = OsFunctions::getSeparator();
                $phpPath = OsFunctions::getPHPExecutableFromPath();

                // Questo codice per versioni che usano un symfony 2 o 3
                if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
                    $command = 'cd ' . $this->apppaths->getRootPath() . $sepchr .
                            $phpPath . ' ' . 'vendor' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'phpunit';
                } else {
                    $command = 'cd ' . $this->apppaths->getRootPath() . $sepchr .
                            $phpPath . ' ' . 'bin' . DIRECTORY_SEPARATOR . 'phpunit -c app';
                }

                $process = new Process($command);
                $process->run();

                $this->locksystem->release();
                // eseguito deopo la fine del comando
                /* if (!$process->isSuccessful()) {
                  return new Response('Errore nel comando: <i style = "color: white;">' .
                 * $command . '</i><br/><i style = "color: red;">' . str_replace("\n",
                 * '<br/>', $process->getErrorOutput()) . '</i>');
                  } */
                $responseout = '<pre>Eseguito comando: <i style = "color: white;">' . $command . '</i><br/>' .
                        str_replace("\n", '<br/>', $process->getOutput()) . '</pre>';

                return new Response($responseout);
            } else {
                return new Response('Non previsto in ambiente windows!');
            }
        }
    }

    public function changethemeAction(Request $request)
    {
        set_time_limit(0);
        $this->apppaths = $this->get("pannelloamministrazione.projectpath");
        $temascelto = $request->get("theme");
        $envfile = $this->apppaths->getRootPath() . '/.env';
        if (!$this->locksystem->acquire()) {
            return new Response($this->getLockMessage());
        } else {
            $this->locksystem->acquire();
            $replacedenv = preg_replace('~^temascelto=.*$~m', "temascelto=" . $temascelto, file_get_contents($envfile));
            file_put_contents($envfile, $replacedenv);

            $this->locksystem->release();
            $responseout = '<pre>Thema selezionato: <i style = "color: white;">' . $temascelto . '</i>' .
                    '<br/>Aggiorna la pagina per renderlo effettivo<br/></pre>';

            return new Response($responseout);
        }
    }
}

<?php

namespace Fi\PannelloAmministrazioneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Fi\OsBundle\DependencyInjection\OsFunctions;
use Fi\PannelloAmministrazioneBundle\DependencyInjection\Commands;
use Fi\PannelloAmministrazioneBundle\DependencyInjection\PannelloAmministrazioneUtils;
use Fi\PannelloAmministrazioneBundle\DependencyInjection\LockSystem;
use Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath;

class PannelloAmministrazioneController extends Controller
{

    protected $apppaths;

    public function indexAction()
    {
        $finder = new Finder();
        $fs = new Filesystem();
        $this->apppaths = new ProjectPath($this->container);

        $projectDir = $this->apppaths->getRootPath();
        $bundlelists = $this->container->getParameter('kernel.bundles');
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
            $delfoldercmd = 'rm -rf';
            $setfilelock = "touch " .$this->getParameter("maintenanceLockFilePath");
            $remfilelock = "rm " .$this->getParameter("maintenanceLockFilePath");
            $windows = false;
            
        } else {
            $delcmd = 'del';
            $delfoldercmd = 'rmdir /s';
            $setfilelock = 'echo $null >> ' .$this->getParameter("maintenanceLockFilePath");
            $remfilelock = "del " .$this->getParameter("maintenanceLockFilePath");
            $windows = true;
        }

        $dellockfile = $delcmd . ' ' . $this->apppaths->getCachePath() . DIRECTORY_SEPARATOR . 'running.run';
        $delcomposerfile = $delcmd . ' ' . $projectDir . DIRECTORY_SEPARATOR . 'composer.lock';
        $dellogsfiles = $delcmd . ' ' . $this->apppaths->getLogsPath() . DIRECTORY_SEPARATOR . '*';
        $delcacheprodfiles = $delcmd . ' ' . $this->apppaths->getCachePath() . DIRECTORY_SEPARATOR . 'prod' . DIRECTORY_SEPARATOR . '*';
        $delcachedevfiles = $delcmd . ' ' . $this->apppaths->getCachePath() . DIRECTORY_SEPARATOR . 'dev' . DIRECTORY_SEPARATOR . '*';
        $setmaintenancefile = $setfilelock;
        $remmaintenancefile = $remfilelock;       

        $comandishell = array(
            'lockfile' => $this->fixSlash($dellockfile),
            'composerlock' => $this->fixSlash($delcomposerfile),
            'logsfiles' => $this->fixSlash($dellogsfiles),
            'cacheprodfiles' => $this->fixSlash($delcacheprodfiles),
            'cachedevfiles' => $this->fixSlash($delcachedevfiles),
            'setmaintenancefile' => $setmaintenancefile,            
            'remmaintenancefile' => $remmaintenancefile,            
        );

        $twigparms = array('svn' => $svn, 'git' => $git, 'bundles' => $bundles, 'mwbs' => $mwbs,
            'rootdir' => $this->fixSlash($projectDir),
            'comandishell' => $comandishell, 'iswindows' => $windows,);

        return $this->render('PannelloAmministrazioneBundle:PannelloAmministrazione:index.html.twig', $twigparms);
    }

    private function fixSlash($path)
    {
        return str_replace('\\', '\\\\', $path);
    }

    public function aggiornaSchemaDatabaseAction()
    {
        if ((new LockSystem($this->container))->isLockedFile()) {
            return (new LockSystem($this->container))->lockedFunctionMessage();
        } else {
            (new LockSystem($this->container))->lockFile(true);
            $commands = new Commands($this->container);
            $result = $commands->aggiornaSchemaDatabase();

            (new LockSystem($this->container))->lockFile(false);
            $twigparms = array('errcode' => $result['errcode'], 'command' => $result['command'], 'message' => $result['message']);

            return $this->render('PannelloAmministrazioneBundle:PannelloAmministrazione:outputcommand.html.twig', $twigparms);
        }
    }

    /* FORMS */

    public function generateFormCrudAction(Request $request)
    {
        if ((new LockSystem($this->container))->isLockedFile()) {
            return (new LockSystem($this->container))->lockedFunctionMessage();
        } else {
            $bundlename = $request->get('bundlename');
            $entityform = $request->get('entityform');

            (new LockSystem($this->container))->lockFile(true);

            $command = new Commands($this->container);
            $ret = $command->generateFormCrud($bundlename, $entityform);

            (new LockSystem($this->container))->lockFile(false);
            //$retcc = '';
            if ($ret['errcode'] < 0) {
                return new Response($ret['message']);
            } else {
                //$retcc = $command->clearCacheEnv($this->container->get('kernel')->getEnvironment());
            }
            $twigparms = array('errcode' => $ret['errcode'], 'command' => $ret['command'], 'message' => $ret['message']);

            return $this->render('PannelloAmministrazioneBundle:PannelloAmministrazione:outputcommand.html.twig', $twigparms);
        }
    }

    /* ENTITIES */

    public function generateEntityAction(Request $request)
    {
        if ((new LockSystem($this->container))->isLockedFile()) {
            return (new LockSystem($this->container))->lockedFunctionMessage();
        } else {
            (new LockSystem($this->container))->lockFile(true);
            $wbFile = $request->get('file');
            $bundlePath = $request->get('bundle');
            $commands = new Commands($this->container);
            $ret = $commands->generateEntity($wbFile, $bundlePath);
            (new LockSystem($this->container))->lockFile(false);
            return new Response($ret['message']);
        }
    }

    /* ENTITIES */

    public function generateEntityClassAction(Request $request)
    {
        if ((new LockSystem($this->container))->isLockedFile()) {
            return (new LockSystem($this->container))->lockedFunctionMessage();
        } else {
            (new LockSystem($this->container))->lockFile(true);
            $bundlePath = $request->get('bundle');
            $commands = new Commands($this->container);
            $ret = $commands->generateEntityClass($bundlePath);
            (new LockSystem($this->container))->lockFile(false);

            return new Response($ret['message']);
        }
    }

    /* BUNDLE */

    public function generateBundleAction(Request $request)
    {
        $this->apppaths = new ProjectPath($this->container);
        if ((new LockSystem($this->container))->isLockedFile()) {
            return (new LockSystem($this->container))->lockedFunctionMessage();
        } else {
            (new LockSystem($this->container))->lockFile(true);
            $commands = new Commands($this->container);
            $bundleName = $request->get('bundlename');
            $result = $commands->generateBundle($bundleName);
            if ($result["errcode"] >= 0) {
                //$msg = "\nPer abilitare il nuovo bundle nel kernel pulire la cache e aggiornare la pagina";
                //$alert = '<script type="text/javascript">alert("' . $msg . '");location.reload();</script>';
                //$result['message'] = $result['message'] . $msg;
            }
            (new LockSystem($this->container))->lockFile(false);
            //Uso exit perchè la render avendo creato un nuovo bundle schianta perchè non è caricato nel kernel il nuovo bundle ancora
            //exit;
            $twigparms = array('errcode' => $result['errcode'], 'command' => $result['command'], 'message' => $result['message']);

            //$commands->clearcache();
            //$this->container->get('kernel')->shutdown();
            //$this->container->get('kernel')->boot();

            return $this->render('PannelloAmministrazioneBundle:PannelloAmministrazione:outputcommand.html.twig', $twigparms);
        }
    }

    /* VCS (GIT,SVN) */

    public function getVcsAction()
    {
        set_time_limit(0);
        $this->apppaths = new ProjectPath($this->container);
        if ((new LockSystem($this->container))->isLockedFile()) {
            return (new LockSystem($this->container))->lockedFunctionMessage();
        } else {
            (new LockSystem($this->container))->lockFile(true);
            $commands = new Commands($this->container);
            $result = $commands->getVcs();
            (new LockSystem($this->container))->lockFile(false);
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
     * @SuppressWarnings(PHPMD)
     */
    public function clearCacheAction(Request $request)
    {
        set_time_limit(0);
        if ((new LockSystem($this->container))->isLockedFile()) {
            return (new LockSystem($this->container))->lockedFunctionMessage();
        } else {
            (new LockSystem($this->container))->lockFile(true);
            $commands = new Commands($this->container);
            $result = $commands->clearcache();

            (new LockSystem($this->container))->lockFile(false);

            /* Uso exit perchè new response avendo cancellato la cache schianta non avendo più a disposizione i file */
            //return $commanddev . '<br/>' . $cmdoutputdev . '<br/><br/>' . $commandprod . '<br/>' . $cmdoutputprod;
            //return new Response(nl2br($result));
            exit(nl2br($result));
        }
    }

    /* CLEAR CACHE */

    public function symfonyCommandAction(Request $request)
    {
        set_time_limit(0);
        $comando = $request->get('symfonycommand');
        if ((new LockSystem($this->container))->isLockedFile()) {
            return (new LockSystem($this->container))->lockedFunctionMessage();
        } else {
            (new LockSystem($this->container))->lockFile(true);
            $this->apppaths = new ProjectPath($this->container);
            $pammutils = new PannelloAmministrazioneUtils($this->container);
            $phpPath = OsFunctions::getPHPExecutableFromPath();
            $result = $pammutils->runCommand($phpPath . ' ' . $this->apppaths->getConsole() . ' ' . $comando);

            (new LockSystem($this->container))->lockFile(false);
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
        if (!OsFunctions::isWindows()) {
            $lockdelcmd = 'rm -rf ';
        } else {
            $lockdelcmd = 'del ';
        }
        //Se viene lanciato il comando per cancellare il file di lock su bypassa tutto e si lancia
        $filelock = str_replace('\\', '\\\\', (new LockSystem($this->container))->getFileLock());
        if (str_replace('\\\\', '/', $command) == str_replace('\\\\', '\\', $lockdelcmd . $filelock)) {
            $fs = new Filesystem();
            if ((!($fs->exists($filelock)))) {
                return new Response('Non esiste il file di lock: <i style = "color: white;">' . $filelock . '</i><br/>');
            } else {
                $result = $pammutils->runCommand($command);

                // eseguito deopo la fine del comando
                if ($result['errmsg'] < 0) {
                    $responseout = 'Errore nel comando: <i style = "color: white;">' .
                            str_replace(';', '<br/>', str_replace('&&', '<br/>', $command)) .
                            '</i><br/><i style = "color: red;">' . str_replace("\n", '<br/>', $result['errmsg']) . '</i>';

                    exit(nl2br($responseout));
                }

                return new Response('File di lock cancellato');
            }
        }

        if ((new LockSystem($this->container))->isLockedFile()) {
            return (new LockSystem($this->container))->lockedFunctionMessage();
        } else {
            (new LockSystem($this->container))->lockFile(true);
            //$phpPath = OsFunctions::getPHPExecutableFromPath();
            $result = $pammutils->runCommand($command);

            (new LockSystem($this->container))->lockFile(false);
            // eseguito deopo la fine del comando
            if ($result['errcode'] < 0) {
                $errmsg = 'Errore nel comando: <i style = "color: white;">' .
                        str_replace(';', '<br/>', str_replace('&&', '<br/>', $command)) .
                        '</i><br/><i style = "color: red;">' . nl2br($result['errmsg']) . '</i>';

                //return new Response($errmsg);
                exit(nl2br($errmsg));
                //Uso exit perchè new response avendo cancellato la cache schianta non avendo più a disposizione i file
                //return;
                /* return new Response('Errore nel comando: <i style = "color: white;">' .
                 * $command . '</i><br/><i style = "color: red;">' . str_replace("\n", '<br/>', $process->getErrorOutput()) . '</i>'); */
            }
            $msgok = '<pre>Eseguito comando:<br/><i style = "color: white;"><br/>' .
                    str_replace(';', '<br/>', str_replace('&&', '<br/>', $command)) . '</i><br/>' .
                    nl2br($result['errmsg']) . '</pre>';
            //Uso exit perchè new response avendo cancellato la cache schianta non avendo più a disposizione i file
            //return new Response($msgok);
            exit(nl2br($msgok));
            //return;
            /* return new Response('<pre>Eseguito comando: <i style = "color: white;">' . $command .
             * '</i><br/>' . str_replace("\n", "<br/>", $process->getOutput()) . "</pre>"); */
        }
    }

    public function phpunittestAction(Request $request)
    {
        set_time_limit(0);
        $this->apppaths = new ProjectPath($this->container);
        if ((new LockSystem($this->container))->isLockedFile()) {
            return (new LockSystem($this->container))->lockedFunctionMessage();
        } else {
            if (!OsFunctions::isWindows()) {
                (new LockSystem($this->container))->lockFile(true);
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

                (new LockSystem($this->container))->lockFile(false);
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
}

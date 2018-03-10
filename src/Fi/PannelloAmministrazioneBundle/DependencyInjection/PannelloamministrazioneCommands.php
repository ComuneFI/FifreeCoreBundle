<?php

namespace Fi\PannelloAmministrazioneBundle\DependencyInjection;

use Symfony\Component\Filesystem\Filesystem;
use Fi\OsBundle\DependencyInjection\OsFunctions;

class PannelloamministrazioneCommands
{

    private $container;
    private $apppaths;
    private $pammutils;

    public function __construct($container)
    {
        $this->container = $container;
        $this->apppaths = $container->get("pannelloamministrazione.projectpath");
        $this->pammutils = $container->get("pannelloamministrazione.utils");
    }

    public function getVcs()
    {
        $fs = new Filesystem();

        $sepchr = OsFunctions::getSeparator();
        $projectDir = $this->apppaths->getRootPath();
        $vcscommand = "";
        if ($fs->exists($projectDir . DIRECTORY_SEPARATOR . '.svn')) {
            $vcscommand = 'svn update';
        }
        if ($fs->exists($projectDir . DIRECTORY_SEPARATOR . '.git')) {
            $vcscommand = 'git pull';
        }
        if (!$vcscommand) {
            throw new \Exception("Vcs non trovato", 100);
        }
        $command = 'cd ' . $projectDir . $sepchr . $vcscommand;
        return $this->pammutils->runCommand($command);
    }

    public function generateBundle($bundleName)
    {
        /* @var $fs \Symfony\Component\Filesystem\Filesystem */
        $fs = new Filesystem();

        $srcPath = $this->apppaths->getSrcPath();
        $appPath = $this->apppaths->getAppPath();

        $bundlePath = $this->apppaths->getSrcPath() . DIRECTORY_SEPARATOR . $bundleName;

        if ($fs->exists($bundlePath)) {
            return array('errcode' => -1, 'command' => 'generate:bundle', 'message' => sprintf("Il bundle esiste gia' in %s", $bundlePath));
        }

        $commandparms = array(
            '--namespace' => $bundleName,
            '--dir' => $srcPath . DIRECTORY_SEPARATOR,
            '--format' => 'yml',
            '--no-interaction' => true,
        );
        $result = $this->pammutils->runSymfonyCommand('generate:bundle', $commandparms);
        $bundlePath = $srcPath . DIRECTORY_SEPARATOR . $bundleName;
        if ($fs->exists($bundlePath)) {
            $routesyaml = $appPath . "/config/routes.yaml";
            if (file_exists($routesyaml)) {
                $bundlestr = strtolower(str_replace("/", "_", $bundleName));
                $routingstr = $bundlestr . ":\n    resource: \"@FiProvaBundle/Resources/config/routing.yml\"\n    prefix:   /\n\n";
                $file_data = $routingstr . file_get_contents($routesyaml);
                file_put_contents($routesyaml, $file_data);
            }
            $addmessage = 'Per abilitare il nuovo bundle nel kernel controllare che sia presente in app/Kernel.php, '
                    . 'pulire la cache e aggiornare la pagina';
            $ret = array('errcode' => 0, 'command' => 'generate:bundle', 'message' => $result["message"] . $addmessage);
        } else {
            $addmessage = "Non e' stato creato il bundle in $bundlePath";
            $ret = array('errcode' => -1, 'command' => 'generate:bundle', 'message' => $result["message"] . $addmessage);
        }
        return $ret;
    }

    public function generateEntity($wbFile)
    {
        $command = "pannelloamministrazione:generateymlentities";
        $result = $this->pammutils->runSymfonyCommand($command, array('mwbfile' => $wbFile));

        if ($result["errcode"] != 0) {
            return array(
                'errcode' => -1,
                'message' => 'Errore nel comando: <i style="color: white;">' .
                $command . '</i><br/><i style="color: red;">' .
                str_replace("\n", '<br/>', $result["message"]) .
                'in caso di errori eseguire il comando symfony non da web: pannelloamministrazione:generateymlentities ' .
                $wbFile . '<br/></i>',
            );
        }

        return array(
            'errcode' => 0,
            'message' => '<pre>Eseguito comando: <i style = "color: white;">' .
            $command . '</i><br/>' . str_replace("\n", '<br/>', $result["message"]) . '</pre>',);
    }

    public function generateEntityClass($bundlePath)
    {
        $command = "pannelloamministrazione:generateentities";
        $result = $this->pammutils->runSymfonyCommand($command, array('bundlename' => $bundlePath));

        if ($result["errcode"] != 0) {
            return array(
                'errcode' => -1,
                'message' => 'Errore nel comando: <i style="color: white;">' .
                $command . '</i><br/><i style="color: red;">' .
                str_replace("\n", '<br/>', $result["message"]) .
                'in caso di errori eseguire il comando symfony non da web: pannelloamministrazione:generateentities ' .
                $bundlePath . '<br/>Opzione --schemaupdate oer aggiornare anche lo schema database</i>',
            );
        }

        return array(
            'errcode' => 0,
            'message' => '<pre>Eseguito comando: <i style = "color: white;">' .
            $command . '</i><br/>' . str_replace("\n", '<br/>', $result["message"]) . '</pre>',);
    }

    public function generateFormCrud($bundlename, $entityform)
    {
        /* @var $fs \Symfony\Component\Filesystem\Filesystem */
        $resultchk = $this->checkFormCrud($bundlename, $entityform);

        if ($resultchk["errcode"] != 0) {
            return $resultchk;
        }
        $formcrudparms = array("bundlename" => $bundlename, "entityform" => $entityform);

        $retmsggenerateform = $this->pammutils->runSymfonyCommand('pannelloamministrazione:generateformcrud', $formcrudparms);

        $retmsg = array(
            'errcode' => $retmsggenerateform['errcode'],
            'command' => $retmsggenerateform['command'],
            'message' => $retmsggenerateform['message'],
        );

        return $retmsg;
    }

    public function checkFormCrud($bundlename, $entityform)
    {
        /* @var $fs \Symfony\Component\Filesystem\Filesystem */
        $fs = new Filesystem();
        $srcPath = $this->apppaths->getSrcPath();
        $appPath = $this->apppaths->getAppPath();
        if (!is_writable($appPath)) {
            return array('errcode' => -1, 'message' => $appPath . ' non scrivibile');
        }
        $formPath = $srcPath . DIRECTORY_SEPARATOR . $bundlename . DIRECTORY_SEPARATOR .
                'Form' . DIRECTORY_SEPARATOR . $entityform . 'Type.php';

        if ($fs->exists($formPath)) {
            return array('errcode' => -1, 'message' => $formPath . ' esistente');
        }

        $controllerPath = $srcPath . DIRECTORY_SEPARATOR . $bundlename . DIRECTORY_SEPARATOR .
                'Controller' . DIRECTORY_SEPARATOR . $entityform . 'Controller.php';

        if ($fs->exists($controllerPath)) {
            return array('errcode' => -1, 'message' => $controllerPath . ' esistente');
        }

        $viewPathSrc = $srcPath . DIRECTORY_SEPARATOR . $bundlename . DIRECTORY_SEPARATOR .
                'Resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $entityform;

        if ($fs->exists($viewPathSrc)) {
            return array('errcode' => -1, 'message' => $viewPathSrc . ' esistente');
        }

        return array('errcode' => 0, 'message' => 'OK');
    }

    public function clearcache()
    {
        $cmdoutput = "";
        $envs = array("dev", "test", "prod");
        foreach ($envs as $env) {
            $cmdoutput = $cmdoutput . $this->clearcacheEnv($env);
        }
        //$cmdoutput = $cmdoutput . $this->clearcacheEnv($this->container->get('kernel')->getEnvironment());

        return $cmdoutput;
    }

    public function clearcacheEnv($env)
    {
        $ret = $this->pammutils->clearcache($env);

        return $ret["errmsg"];
    }

    public function aggiornaSchemaDatabase()
    {
        $result = $this->pammutils->runSymfonyCommand('doctrine:schema:update', array('--force' => true));

        return $result;
    }
}

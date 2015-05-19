<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class fifree2fixpermissionCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('fifree2:fixpermission')
                ->setDescription('Correzione permessi ambiente fifree')
                ->setHelp('Sistema i privilegi delle cartelle del progetto')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $rootdir = $this->getContainer()->get('kernel')->getRootDir() . "/..";
        $appdir = $this->getContainer()->get('kernel')->getRootDir();
        $cachedir = $appdir . DIRECTORY_SEPARATOR . "cache";
        $logdir = $appdir . DIRECTORY_SEPARATOR . "logs";
        $tmpdir = $appdir . DIRECTORY_SEPARATOR . "tmp";
        $srcdir = $rootdir . DIRECTORY_SEPARATOR . "src";
        $webdir = $rootdir . DIRECTORY_SEPARATOR . "web";
        $uploaddir = $webdir . DIRECTORY_SEPARATOR . "uploads";


        $phpcmd = $this->getPHPExecutableFromPath();
        if (self::isWindows()) {
            echo "Non previsto in ambiente windows";
            exit;
        }

        /* $commandutil = $phpcmd . " " .$appdir . DIRECTORY_SEPARATOR . "console cache:clear --env=dev";
          echo shell_exec($commandutil . " 2>&1");  //system call
          $commandutil = $phpcmd . " " .$appdir . DIRECTORY_SEPARATOR . "console cache:clear --env=test";
          echo shell_exec($commandutil . " 2>&1");  //system call
          $commandutil = $phpcmd . " " .$appdir . DIRECTORY_SEPARATOR . "console cache:clear --env=prod";
          echo shell_exec($commandutil . " 2>&1");  //system call
         */
        $commandutil = 'sudo chown -R $(stat -c "%U:%G" ' . $rootdir . ') ' . $rootdir;
        echo $commandutil . "\n";
        
        
        
        $commandutil = "APACHEUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data' | grep -v root | head -1 | cut -d\  -f1`";
        echo $commandutil . "\n";
        $commandutil = 'sudo setfacl -R -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX ' . $cachedir;
        echo $commandutil . "\n";
        $commandutil = 'sudo setfacl -R -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX ' . $logdir;
        echo $commandutil . "\n";
        $commandutil = 'sudo setfacl -dR -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX ' . $cachedir;
        echo $commandutil . "\n";
        $commandutil = 'sudo setfacl -dR -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX ' . $logdir;
        echo $commandutil . "\n";


        //Si da il 755 alla cartella principale e sottocartelle del progetto
        $commandutil = 'sudo chmod -R 755 ' . $rootdir;
        //$outputshell = shell_exec($commandutil . " 2>&1");  //system call
        //echo $outputshell;
        echo $commandutil . "\n";

        //Si da il full access alla cartella principale e sottocartelle tmp
        $commandutil = 'sudo chmod -R 777 ' . $tmpdir;
        //$outputshell = shell_exec($commandutil . " 2>&1");  //system call
        //echo $outputshell;
        echo $commandutil . "\n";

        //Si da il full access solo alla cartella principale src
        $commandutil = 'sudo chmod 777 ' . $srcdir;
        //$outputshell = shell_exec($commandutil . " 2>&1");  //system call
        //echo $outputshell;
        echo $commandutil . "\n";

        //Si da il full access alla cartella principale e sottocartelle log
        $commandutil = 'sudo chmod -R 777 ' . $logdir;
        //$outputshell = shell_exec($commandutil . " 2>&1");  //system call
        //echo $outputshell;
        echo $commandutil . "\n";

        //Si da il full access alla cartella principale e sottocartelle cache
        $commandutil = 'sudo chmod -R 777 ' . $cachedir;
        //$outputshell = shell_exec($commandutil . " 2>&1");  //system call
        //echo $outputshell;
        echo $commandutil . "\n";

        //Si da il full access alla cartella principale e sottocartelle upload
        $commandutil = 'sudo chmod -R 777 ' . $uploaddir;
        //$outputshell = shell_exec($commandutil . " 2>&1");  //system call
        //echo $outputshell;
        echo $commandutil . "\n";
    }

    static function getPHPExecutableFromPath() {
        $phpPath = exec("which php");
        if (file_exists($phpPath)) {
            return $phpPath;
        } elseif (file_exists("/usr/bin/php")) {
            return "/usr/bin/php";
        }
        $paths = explode(PATH_SEPARATOR, getenv('PATH'));
        foreach ($paths as $path) {
            $php_executable = $path . DIRECTORY_SEPARATOR . "php" . (isset($_SERVER["WINDIR"]) ? ".exe" : "");
            if (file_exists($php_executable) && is_file($php_executable)) {
                return $php_executable;
            }
        }
        echo "Php non trovato";
        return FALSE; // not found
    }

    static function isWindows() {
        if (PHP_OS == "WINNT") {
            return true;
        } else {
            return false;
        }
    }

}

?>
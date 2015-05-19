<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class fifree2checkpermissionCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('fifree2:checkpermission')
                ->setDescription('Controllo permessi ambiente fifree')
                ->setHelp('Controlla i privilegi delle cartelle del progetto')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $rootdir = $this->getContainer()->get('kernel')->getRootDir() . "/..";
        $appdir = $this->getContainer()->get('kernel')->getRootDir();
        $cachedir = $appdir . DIRECTORY_SEPARATOR . "cache";
        $logdir = $appdir . DIRECTORY_SEPARATOR . "logs";
        $tmpdir = $appdir . DIRECTORY_SEPARATOR . "/tmp";
        $srcdir = $rootdir . DIRECTORY_SEPARATOR . "/src";
        $webdir = $rootdir . DIRECTORY_SEPARATOR . "/web";
        $uploaddir = $webdir . DIRECTORY_SEPARATOR . "/uploads";


        $phpcmd = $this->getPHPExecutableFromPath();
        if (self::isWindows()) {
            echo "Non previsto in ambiente windows";
            exit;
        }

        //Si da il 755 alla cartella principale e sottocartelle del progetto
        if (!is_writable($rootdir)) {
            echo $rootdir . " non scrivibile";
        }

        if (!is_writable($tmpdir)) {
            echo $tmpdir . " non scrivibile";
        }
        if (!is_writable($srcdir)) {
            echo $srcdir . " non scrivibile";
        }

        if (!is_writable($logdir)) {
            echo $logdir . " non scrivibile";
        }
        
        if (file_exists($uploaddir) && !is_writable($uploaddir)) {
            echo $uploaddir . " non scrivibile";
        }

        
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
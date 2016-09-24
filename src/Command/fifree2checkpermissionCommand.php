<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fi\OsBundle\DependencyInjection\OsFunctions;

class fifree2checkpermissionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fifree2:checkpermission')
            ->setDescription('Controllo permessi ambiente fifree')
            ->setHelp('Controlla i privilegi delle cartelle del progetto');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootdir = $this->getContainer()->get('kernel')->getRootDir().'/..';
        $appdir = $this->getContainer()->get('kernel')->getRootDir();
        $logdir = $appdir.DIRECTORY_SEPARATOR.'logs';
        $tmpdir = $appdir.DIRECTORY_SEPARATOR.'/tmp';
        $srcdir = $rootdir.DIRECTORY_SEPARATOR.'/src';
        $webdir = $rootdir.DIRECTORY_SEPARATOR.'/web';
        $uploaddir = $webdir.DIRECTORY_SEPARATOR.'/uploads';

        if (OsFunctions::isWindows()) {
            echo 'Non previsto in ambiente windows';
            return 1;
        }

        //Si da il 755 alla cartella principale e sottocartelle del progetto
        if (!is_writable($rootdir)) {
            echo $rootdir.' non scrivibile';
        }

        if (!is_writable($tmpdir)) {
            echo $tmpdir.' non scrivibile';
        }
        if (!is_writable($srcdir)) {
            echo $srcdir.' non scrivibile';
        }

        if (!is_writable($logdir)) {
            echo $logdir.' non scrivibile';
        }

        if (file_exists($uploaddir) && !is_writable($uploaddir)) {
            echo $uploaddir.' non scrivibile';
        }
    }
}

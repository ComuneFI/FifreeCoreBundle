<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class Fifree2pubblicamanualeCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('fifree2:pubblicamanuale')
                ->setDescription('Copia il manuale dalla cartella Doc alla cartella Web')
                ->setHelp('Estende la pubblicazione degli assets al manuale');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->copiaManuale();
    }

    protected function copiaManuale()
    {
        $filesystem = $this->getContainer()->get('filesystem');

        $projectDir = substr($this->getContainer()->get('kernel')->getRootDir(), 0, -4);
        $originDir = $projectDir . '/doc/manuale';
        $targetDir = $projectDir . '/web/uploads';

        $filesystem->mkdir($targetDir, 0777);
        //    // We use a custom iterator to ignore VCS files
        $filesystem->mirror($originDir, $targetDir, Finder::create()->name('manuale.pdf')->in($originDir));
    }
}

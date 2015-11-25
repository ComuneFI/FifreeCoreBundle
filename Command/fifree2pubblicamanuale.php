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
    $this->copiaManuale($input);
  }

  protected function copiaManuale($input) {

    $filesystem = $this->getContainer()->get('filesystem');

    $projectDir = substr($this->getContainer()->get('kernel')->getRootDir(), 0, -4);
    $originDir = $projectDir . "/doc/manuale";
    $targetDir = $projectDir . "/web/uploads";

    $filesystem->mkdir($targetDir, 0777);
//    // We use a custom iterator to ignore VCS files
    $filesystem->mirror($originDir, $targetDir, Finder::create()->name("manuale.pdf")->in($originDir));
  }

}

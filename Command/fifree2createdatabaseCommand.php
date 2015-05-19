<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class fifree2createdatabaseCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('fifree2:createdatabase')
                ->setDescription('Creazione database fifree')
                ->setHelp('Creazione di un nuovo database di fifree')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        
        $command = $this->getApplication()->find('doctrine:database:create');
        $arguments = array('');
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
    }

}

?>
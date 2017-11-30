<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class Fifree2createdatabaseCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('fifree2:createdatabase')
                ->setDescription('Creazione database fifree')
                ->setHelp('Creazione di un nuovo database di fifree');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $driver = $em->getConnection()->getDriver()->getName();


        if ($driver != "pdo_sqlite") {
            $command = $this->getApplication()->find('doctrine:database:create');
            $arguments = array('--if-not-exists' => true);
            $inputcmd = new ArrayInput($arguments);
            $command->run($inputcmd, $output);
        }
        $command = $this->getApplication()->find('doctrine:schema:create');
        $arguments = array('');
        $inputcmd = new ArrayInput($arguments);
        $command->run($inputcmd, $output);
    }

}

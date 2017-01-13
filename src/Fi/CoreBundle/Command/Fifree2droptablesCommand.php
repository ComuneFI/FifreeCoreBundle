<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Fifree2droptablesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('fifree2:droptables')
                ->setDescription('Eliminazione di tutte le tabelle fifree2')
                ->setHelp('ATTENZIONE, questo comando cancellerà tutte le informazioni presenti nel database!!')
                ->addOption('force', null, InputOption::VALUE_NONE, 'Se non impostato, il comando non avrà effetto');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $driver = $em->getConnection()->getDriver()->getName();

        if ($driver != "pdo_mysql") {
            $output->writeln("Non previsto per driver: " . $driver);
            return 1;
        }

        $force = $input->getOption('force');

        if (!$force) {
            $output->writeln("Specificare l'opzione --force per eseguire il comando");
            return 1;
        }

        //$this->dbh->query(sprintf('SET FOREIGN_KEY_CHECKS = 0;'));
        //Truncate tabelle
        $tables = $em->getConnection()->getSchemaManager()->listTables();
        foreach ($tables as $table) {
            $tableName = $table->getName();
            $em->getConnection()->executeQuery(sprintf('TRUNCATE TABLE %s CASCADE', $tableName));
        }
        //Cancellazione tabelle
        foreach ($tables as $table) {
            $tableName = $table->getName();
            $em->getConnection()->executeQuery(sprintf('DROP TABLE %s CASCADE', $tableName));
        }
        //Cancellazione sequences
        $sequences = $em->getConnection()->getSchemaManager()->listSequences();
        foreach ($sequences as $sequence) {
            $sequenceName = $sequence->getName();
            $em->getConnection()->executeQuery(sprintf('DROP SEQUENCE %s', $sequenceName));
        }
    }
}

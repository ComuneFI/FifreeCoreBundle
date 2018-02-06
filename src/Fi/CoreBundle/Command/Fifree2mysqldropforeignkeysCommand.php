<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Fifree2mysqldropforeignkeysCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('fifree2:mysqldropforeignkeys')
                ->setDescription('Cancella le foreign keys dal db')
        //->setHelp('Modifica il motore mysql delle tabelle')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $inizio = microtime(true);
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $this->getContainer()->get('database_connection')->getDatabase();

        $driver = $em->getConnection()->getDriver()->getName();

        if ($driver != "pdo_mysql") {
            $output->writeln("Non previsto per driver: " . $driver);
            return 1;
        }

        $sql = "select concat('alter table ',table_schema,'.',table_name,' DROP FOREIGN KEY ',constraint_name,';') FKNAME
            from information_schema.table_constraints
            where constraint_type='FOREIGN KEY'";
        $conn = $em->getConnection();
        $rows = $conn->fetchAll($sql);
        foreach ($rows as $row) {
            $fk = $row['FKNAME'];
            $sqlalter = $fk;
            $output->writeln($sqlalter);
            $conn->exec($sqlalter);
        }

        $fine = microtime(true);
        $tempo = gmdate('H:i:s', $fine - $inizio);

        $text = 'Fine in ' . $tempo . ' secondi';
        $output->writeln($text);
    }
}

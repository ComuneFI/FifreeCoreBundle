<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Fifree2mysqlconvertdbengineCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fifree2:mysqlconvertdbengine')
            ->setDescription('Converte il motore delle tabelle mysql')
            ->addArgument('engine', InputArgument::REQUIRED, 'Specificare il tipo di motore che si vuole (MyISAM, INNODB, ecc)')
            ->addOption('tablesfifree2', null, InputOption::VALUE_OPTIONAL, 'Si devono trattare anche le tabelle di fifree2', false)
            ->setHelp('Modifica il motore mysql delle tabelle');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $inizio = microtime(true);
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $engine = $input->getArgument('engine');
        $tablesfifree2 = $input->getOption('tablesfifree2');
        $dbname = $this->getContainer()->get('database_connection')->getDatabase();

        $sql = "SELECT TABLE_NAME
                FROM information_schema.TABLES 
                WHERE
                  TABLE_TYPE='BASE TABLE'
                  AND TABLE_SCHEMA='" .$dbname."'";
        $conn = $em->getConnection();
        $rows = $conn->fetchAll($sql);
        foreach ($rows as $row) {
            $tbl = $row['TABLE_NAME'];
            if (substr($tbl, 0, 2) == '__' && !($tablesfifree2)) {
                continue;
            }

            $sqlalter = "ALTER TABLE $tbl ENGINE=$engine";
            $output->writeln($sqlalter);
            $conn->exec($sqlalter);
        }

        //var_dump($sql);exit;
        $fine = microtime(true);
        $tempo = gmdate('H:i:s', $fine - $inizio);

        $text = 'Fine in '.$tempo.' secondi';
        $output->writeln($text);
    }
}

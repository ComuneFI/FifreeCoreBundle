<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class fifree2mysqltruncatetablesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fifree2:mysqltruncatetables')
            ->setDescription('Tronca tutte le tabelle mysql')
            ->addOption('tablesfifree2', null, InputOption::VALUE_OPTIONAL, 'Si devono trattare anche le tabelle di fifree2', false)
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
        $tablesfifree2 = $input->getOption('tablesfifree2');

        $dbname = $this->getContainer()->get('database_connection')->getDatabase();

        $sql = "SELECT Concat('TRUNCATE TABLE ',table_schema,'.',TABLE_NAME, ';') TRUNCTABLE, TABLE_NAME FROM INFORMATION_SCHEMA.TABLES where  table_schema in ('".$dbname."')";
        $conn = $em->getConnection();
        $rows = $conn->fetchAll($sql);
        foreach ($rows as $row) {
            $tbl = $row['TRUNCTABLE'];
            if (substr($row['TABLE_NAME'], 0, 2) == '__' && !($tablesfifree2)) {
                continue;
            }

            $sqlalter = $tbl;
            $output->writeln($sqlalter);
            $conn->exec($sqlalter);
        }

        $fine = microtime(true);
        $tempo = gmdate('H:i:s', $fine - $inizio);

        $text = 'Fine in '.$tempo.' secondi';
        $output->writeln($text);
    }
}

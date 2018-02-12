<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Persistence\Proxy;

class Fifree2configuratorexportCommand extends ContainerAwareCommand
{

    private $entities = array();

    protected function configure()
    {
        $this
                ->setName('fifree2:configuratorexport')
                ->setDescription('Configuratore per Fifree')
                ->setHelp('Esporta la configurazione di fifree')
                ->addArgument('entity', InputArgument::REQUIRED, 'Entity da esportare')
                ->addOption('append', null, InputOption::VALUE_NONE, 'Append per export');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem;
        $append = $input->getOption('append');
        $this->em = $this->getContainer()->get("doctrine")->getManager();
        $this->output = $output;
        $entity = $input->getArgument('entity');

        try {
            //$fixturefile = $this->getContainer()->get('kernel')->locateResource('@FiCoreBundle/Resources/config/fixtures.yml');
            $fixturefile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "fixtures.yml";
            if (!$append) {
                $fs->remove($fixturefile);
            }
            return $this->export($fixturefile, $entity);
        } catch (\Exception $exc) {
            echo $exc->getMessage() . " at line " . $exc->getLine();
        }
    }

    protected function export($fixturefile, $entity)
    {
        $entityclass = "Fi\\CoreBundle\\Entity\\" . $entity;
        $ret = $this->exportEntity($fixturefile, $entityclass);
        if ($ret == 0) {
            foreach ($this->entities as $entity) {
                $this->output->writeln("<info>Esporto " . $entity . " su file</info>");
                $this->exportEntityToFile($fixturefile, $entity);
            }
            $this->exportEntityToFile($fixturefile, $entityclass);
            return 0;
        }
        return 1;
    }

    private function exportEntity($fixturefile, $entityclass)
    {

        $this->output->writeln("<info>Export Entity: " . $entityclass . "</info>");
        if ($this->entityExists($entityclass)) {
            $hasEntityCollegate = $this->hasJoinTables($entityclass);
            if ($hasEntityCollegate) {
                $this->output->writeln("<info>Entity " . $entityclass . " ha tabelle in join</info>");
                $entityCollegata = $this->entityJoinTables($entityclass);
                foreach ($entityCollegata as $key => $tabella) {
                    $this->entities[] = $key;
                    $this->output->writeln("<info>Prima esporto " . $key . " -> " . $tabella["entity"]["fieldName"] . "</info>");
                    $this->exportEntity($fixturefile, $key);
                }
            }
        } else {
            $this->output->writeln("<error>Entity not found: " . $entityclass . " </error>");
            return 1;
        }
        return 0;
    }

    private function exportEntityToFile($fixturefile, $entityclass)
    {
        $entityDump = array();


        $query = $this->em->createQueryBuilder()
                ->select('p')
                ->from($entityclass, 'p')
                ->getQuery()
        ;
        $repo = $query->getArrayResult();


        //$repo = $this->em->getRepository($entityclass)->findAll();
        $this->output->writeln("<info>Trovate " . count($repo) . " records per l'entity " . $entityclass . "</info>");
        foreach ($repo as $row) {
            $entityDump[$entityclass][] = $row;
        }
        $yml = Yaml::dump($entityDump);
        file_put_contents($fixturefile, $yml, FILE_APPEND);
    }

    private function entityExists($className)
    {

        if (is_object($className)) {
            $className = ($className instanceof Proxy) ? get_parent_class($className) : get_class($className);
        }

        return !$this->em->getMetadataFactory()->isTransient($className);
    }

    private function entityJoinTables($entityclass)
    {
        $jointables = array();
        $metadata = $this->em->getClassMetadata($entityclass);
        $fielsassoc = $metadata->associationMappings;
        foreach ($fielsassoc as $tableassoc) {
            if ($tableassoc["inversedBy"]) {
                $jointables[$tableassoc["targetEntity"]] = array("entity" => $tableassoc);
            }
        }
        return $jointables;
    }

    private function hasJoinTables($entityclass)
    {
        $jointables = $this->entityJoinTables($entityclass);
        return count($jointables) > 0 ? true : false;
    }
}

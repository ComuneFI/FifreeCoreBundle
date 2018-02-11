<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;

class Fifree2configuratorimportCommand extends ContainerAwareCommand
{

    private $forceupdate = false;
    private $verboso = false;

    protected function configure()
    {
        $this
                ->setName('fifree2:configuratorimport')
                ->setDescription('Configuratore per Fifree')
                ->setHelp('Importa la configurazione di fifree da file fixtures.yml')
                ->addOption('forceupdate', null, InputOption::VALUE_NONE, 'Forza update di record con id già presente')
                ->addOption('verboso', null, InputOption::VALUE_NONE, 'Visualizza tutti i messaggi di importazione');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->forceupdate = $input->getOption('forceupdate');
        $this->verboso = $input->getOption('verboso');

        try {
            //$fixturefile = $this->getContainer()->get('kernel')->locateResource('@FiCoreBundle/Resources/config/fixtures.yml');
            $fixturefile = dirname($this->getContainer()->get('kernel')->getRootDir()) . "/doc" . DIRECTORY_SEPARATOR . "fixtures.yml";
            return $this->import($fixturefile);
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    protected function import($fixturefile)
    {
        $fs = new Filesystem;
        if ($fs->exists($fixturefile)) {
            $fixtures = Yaml::parse(file_get_contents($fixturefile));
            $msg = "<info>Trovate " . count($fixtures) . " entities nel file " . $fixturefile . "</info>";
            $this->output->writeln($msg);
            foreach ($fixtures as $entityclass => $fixture) {
                $this->executeImport($entityclass, $fixture);
            }
        }
    }

    private function executeImport($entityclass, $fixture)
    {
        $msg = "<info>Trovati " . count($fixture) . " record per l'entity " . $entityclass . "</info>";
        $this->output->writeln($msg);
        foreach ($fixture as $record) {
            $this->em = $this->getContainer()->get("doctrine")->getManager();
            $objrecord = $this->em->getRepository($entityclass)->find($record["id"]);
            if (count($objrecord) > 0) {
                if ($this->forceupdate) {
                    $retcode = $this->executeUpdate($entityclass, $record, $objrecord);
                    if ($retcode !== 0) {
                        return 1;
                    }
                } else {
                    $msgerr = "<error>" . $entityclass . " con id " . $record["id"]
                            . " non modificata, specificare l'opzione --forceupdate "
                            . "per sovrascrivere record presenti</error>";
                    $this->output->writeln($msgerr);
                }
            } else {
                $retcode = $this->executeInsert($entityclass, $record);
                if ($retcode !== 0) {
                    return 1;
                }
            }
        }
    }

    private function executeInsert($entityclass, $record)
    {
        $objrecord = new $entityclass();

        foreach ($record as $key => $value) {
            if ($key !== 'id' && $value) {
                $propertyEntity = $this->getFieldNameForEntity($key, $objrecord);
                $setfieldname = $propertyEntity["set"];
                $objrecord->$setfieldname($value);
            }
        }
        /* @var $em \Doctrine\ORM\EntityManager */
        $this->em->persist($objrecord);
        $this->em->flush();
        $this->em->clear();
        $infomsg = "<info>" . $entityclass . " con id " . $objrecord->getId() . " aggiunta</info>";
        $this->output->writeln($infomsg);
        if ($record["id"] !== $objrecord->getId()) {
            try {
                $qb = $this->em->createQueryBuilder();
                $q = $qb->update($entityclass, 'u')
                        ->set('u.id', ":newid")
                        ->where('u.id = :oldid')
                        ->setParameter("newid", $record["id"])
                        ->setParameter("oldid", $objrecord->getId())
                        ->getQuery();
                $q->execute();
                $msgok = "<info>" . $entityclass . " con id " . $objrecord->getId() . " sistemata</info>";
                $this->output->writeln($msgok);
            } catch (\Exception $exc) {
                echo $exc->getMessage();
                return 1;
            }
        }
        return 0;
    }

    private function executeUpdate($entityclass, $record, $objrecord)
    {

        foreach ($record as $key => $value) {
            if ($key !== 'id' /* && $value !== null */) {
                //if ($key=='is_user' && $value !== true){var_dump($value);exit;}
                $propertyEntity = $this->getFieldNameForEntity($key, $objrecord);
                $getfieldname = $propertyEntity["get"];
                $setfieldname = $propertyEntity["set"];
                $cambiato = $this->isRecordChanged($entityclass, $key, $objrecord->$getfieldname(), $value);
                if (!$cambiato) {
                    if ($this->verboso) {
                        $msginfo = "<info>" . $entityclass . " con id " . $record["id"]
                                . " per campo " . $key . " non modificato perchè già "
                                . $value . "</info>";
                        $this->output->writeln($msginfo);
                    }
                } else {
                    try {
                        $fieldtype = $this->getFieldType($objrecord, $key);
                        if ($fieldtype === 'datetime') {
                            $date = new \DateTime();
                            $date->setTimestamp($value);
                            $msgok = "<info>" . $entityclass . " con id " . $record["id"]
                                    . " per campo " . $key . " cambio valore da "
                                    //. (!is_null($objrecord->$getfieldname())) ? $objrecord->$getfieldname()->format("Y-m-d H:i:s") : "(null)"
                                    . ($objrecord->$getfieldname() ? $objrecord->$getfieldname()->format("Y-m-d H:i:s") : "")
                                    . " a " . $date->format("Y-m-d H:i:s") . "</info>";
                            $this->output->writeln($msgok);

                            $objrecord->$setfieldname($date);
                            continue;
                        }
                        if (is_array($value)) {
                            $msgarray = "<info>" . $entityclass . " con id " . $record["id"]
                                    . " per campo " . $key . " cambio valore da "
                                    . json_encode($objrecord->$getfieldname()) . " a "
                                    . json_encode($value) . " in formato array" . "</info>";
                            $this->output->writeln($msgarray);
                            $objrecord->$setfieldname($value);
                            continue;
                        }
                        $msgok = "<info>" . $entityclass . " con id " . $record["id"]
                                . " per campo " . $key . " cambio valore da " . print_r($objrecord->$getfieldname(), true)
                                . " a " . print_r($value, true) . "</info>";
                        $this->output->writeln($msgok);
                        $objrecord->$setfieldname($value);
                    } catch (\Exception $exc) {
                        $msgerr = "<error>" . $entityclass . " con id " . $record["id"]
                                . " per campo " . $key . ", ERRORE: " . $exc->getMessage()
                                . " alla riga " . $exc->getLine() . "</error>";
                        $this->output->writeln($msgerr);
                        return 1;
                    }
                }
            }
        }

        $this->em->persist($objrecord);
        $this->em->flush();
        $this->em->clear();
        return 0;
    }

    private function getFieldNameForEntity($fieldname, $objrecord)
    {
        $parametri = array('str' => $fieldname, 'primamaiuscola' => true);
        $getfieldname = \Fi\CoreBundle\Utils\GrigliaUtils::toCamelCase($parametri);
        if (!method_exists($objrecord, $getfieldname)) {
            $getfieldname = "has" . \Fi\CoreBundle\Utils\GrigliaUtils::toCamelCase($parametri);
            if (!method_exists($objrecord, $getfieldname)) {
                $getfieldname = "is" . \Fi\CoreBundle\Utils\GrigliaUtils::toCamelCase($parametri);
                if (!method_exists($objrecord, $getfieldname)) {
                    $getfieldname = "get" . \Fi\CoreBundle\Utils\GrigliaUtils::toCamelCase($parametri);
                }
            }
        }
        $setfieldname = "set" . \Fi\CoreBundle\Utils\GrigliaUtils::toCamelCase($parametri);

        return array("get" => $getfieldname, "set" => $setfieldname);
    }

    private function isRecordChanged($entity, $fieldname, $oldvalue, $newvalue)
    {
        $fieldtype = $this->getFieldType(new $entity(), $fieldname);
        if ($fieldtype === 'datetime') {
            return $this->isDateChanged($oldvalue, $newvalue);
        }
        if (is_array($oldvalue)) {
            return $this->isArrayChanged($oldvalue, $newvalue);
        }

        return ($oldvalue !== $newvalue);
    }

    private function isDateChanged($oldvalue, $newvalue)
    {
        $datenewvalue = new \DateTime();
        $datenewvalue->setTimestamp($newvalue);
        if (!$oldvalue && !$newvalue) {
            return false;
        }
        if ((!$oldvalue && $newvalue) || ($oldvalue && !$newvalue)) {
            return true;
        }
        return ($oldvalue != $datenewvalue);
    }

    private function isArrayChanged($oldvalue, $newvalue)
    {
        if (!$oldvalue && !$newvalue) {
            return false;
        }
        if ((!$oldvalue && $newvalue) || ($oldvalue && !$newvalue)) {
            return true;
        }
        return count(array_diff($oldvalue, $newvalue) > 0);
    }

    private function getFieldType($entity, $field)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $metadata = $em->getClassMetadata(get_class($entity));
        $fieldMetadata = $metadata->fieldMappings[$field];

        $fieldType = $fieldMetadata['type'];
        return $fieldType;
    }
}

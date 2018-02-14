<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class DatabaseUtility
{

    private $container;
    /* @var $em \Doctrine\ORM\EntityManager */
    private $em;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $this->container->get("doctrine")->getManager();
    }

    public function getFieldType($entity, $field)
    {
        $metadata = $this->em->getClassMetadata(get_class($entity));
        $fieldMetadata = $metadata->fieldMappings[$field];

        $fieldType = $fieldMetadata['type'];
        return $fieldType;
    }

    public function isRecordChanged($entity, $fieldname, $oldvalue, $newvalue)
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

    public function isDateChanged($oldvalue, $newvalue)
    {
        $datenewvalue = new \DateTime();
        $datenewvalue->setTimestamp($newvalue);
        $twoboth = !$oldvalue && !$newvalue;
        if ($twoboth) {
            return false;
        }
        $onlyonenull = (!$oldvalue && $newvalue) || ($oldvalue && !$newvalue);
        if ($onlyonenull) {
            return true;
        }
        $changed = ($oldvalue != $datenewvalue);
        return $changed;
    }

    public function isArrayChanged($oldvalue, $newvalue)
    {
        $twoboth = !$oldvalue && !$newvalue;
        if ($twoboth) {
            return false;
        }
        $onlyonenull = (!$oldvalue && $newvalue) || ($oldvalue && !$newvalue);
        if ($onlyonenull) {
            return true;
        }
        $numdiff = array_diff($oldvalue, $newvalue);
        return count($numdiff) > 0;
    }

    public function truncateTable($entityclass, $cascade = false)
    {
        $cmd = $this->em->getClassMetadata($entityclass);
        $connection = $this->em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $dbtype = $connection->getDriver()->getDatabasePlatform()->getName();
        $cascademysql = $cascade && $dbtype === 'mysql';
        if ($cascademysql) {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
        }
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName(), $cascade);
        $connection->executeUpdate($q);
        if ($cascademysql) {
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
        }
        $this->em->clear();
    }
}

<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class DatabaseUtility
{

    private $container;
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

    public function getEntityProperties($fieldname, $objrecord)
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

    public function entityExists($className)
    {

        if (is_object($className)) {
            $className = ($className instanceof Proxy) ? get_parent_class($className) : get_class($className);
        }

        return !$this->em->getMetadataFactory()->isTransient($className);
    }

    public function getEntityJoinTables($entityclass)
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

    public function entityHasJoinTables($entityclass)
    {
        $jointables = $this->getEntityJoinTables($entityclass);
        return count($jointables) > 0 ? true : false;
    }
}

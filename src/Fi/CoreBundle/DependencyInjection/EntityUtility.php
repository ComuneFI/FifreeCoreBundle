<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\Common\Persistence\Proxy;

class EntityUtility
{

    private $container;
    private $em;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $this->container->get("doctrine")->getManager();
    }

    public function getTableFromEntity($entity)
    {
        $metadata = $this->em->getClassMetadata($entity);
        $tablename = $metadata->table["name"];

        return $tablename;
    }

    public function entityHasJoinTables($entityclass)
    {
        $jointables = $this->getEntityJoinTables($entityclass);
        return count($jointables) > 0 ? true : false;
    }

    public function getJoinTableField($entityclass, $field)
    {
        $joinfields = $this->getEntityJoinTables($entityclass);
        foreach ($joinfields as $joinfield) {
            if (count($joinfield) != 1) {
                return null;
            }
            $jointableentity = $this->getJoinTable($joinfield, $field);
            if ($jointableentity) {
                return $jointableentity;
            }
        }
        return null;
    }

    public function getJoinTableFieldProperty($entityclass, $field)
    {
        $joinfields = $this->getEntityJoinTables($entityclass);
        foreach ($joinfields as $joinfield) {
            if (count($joinfield) != 1) {
                return null;
            }
            $joinfieldname = $this->getJoinFieldName($joinfield, $field);
            if ($joinfieldname) {
                return $joinfieldname;
            }
        }
        return null;
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

    private function getJoinFieldName($joinfield, $field)
    {
        $joinFieldentity = $joinfield["entity"];
        $joinColumns = $joinFieldentity["joinColumns"];
        foreach ($joinColumns as $joinColumn) {
            if ($field === $joinColumn["name"]) {
                $joinFieldName = $joinFieldentity["fieldName"];
                return $joinFieldName;
            }
        }
        return null;
    }

    private function getJoinTable($joinfield, $field)
    {
        $joinTableEntity = $joinfield["entity"];
        $joinColumns = $joinTableEntity["joinColumns"];
        foreach ($joinColumns as $joinColumn) {
            if ($field === $joinColumn["name"]) {
                return $joinTableEntity["targetEntity"];
            }
        }
        return null;
    }
}

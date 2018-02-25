<?php

namespace Fi\CoreBundle\Subscriber;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Id\SequenceGenerator;

class TableSchemaSubscriber implements \Doctrine\Common\EventSubscriber
{
    protected $prefix = '';

    public function __construct($prefix)
    {
        $this->prefix = (string) $prefix;
    }

    public function getSubscribedEvents()
    {
        return array('loadClassMetadata');
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        if ($this->prefix != '') {
            $classMetadata = $args->getClassMetadata();

            $classMetadata->setPrimaryTable(array('name' => $this->prefix.'.'.$classMetadata->getTableName()));

            foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
                $jointablename = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                if ($mapping['type'] == ClassMetadataInfo::MANY_TO_MANY && isset($jointablename)) {
                    $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                    $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix.'.'.$mappedTableName;
                }
            }
            if ($classMetadata->isIdGeneratorSequence()) {
                $newDefinition = $classMetadata->sequenceGeneratorDefinition;
                $newDefinition['sequenceName'] = $this->prefix.'.'.$newDefinition['sequenceName'];

                $classMetadata->setSequenceGeneratorDefinition($newDefinition);
                $em = $args->getEntityManager();
                if (isset($classMetadata->idGenerator)) {
                    $sequncename = $em->getConfiguration()->getQuoteStrategy()
                            ->getSequenceName($newDefinition, $classMetadata, $em->getConnection()->getDatabasePlatform());
                    $allocationSize = $newDefinition['allocationSize'];
                    $sequenceGenerator = new SequenceGenerator($sequncename, $allocationSize);
                    $classMetadata->setIdGenerator($sequenceGenerator);
                }
            }
        }
    }
}

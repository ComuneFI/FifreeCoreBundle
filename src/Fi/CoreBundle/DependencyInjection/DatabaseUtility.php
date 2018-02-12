<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class DatabaseUtility
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getFieldType($entity, $field)
    {
        $em = $this->container->get("doctrine")->getManager();
        $metadata = $em->getClassMetadata(get_class($entity));
        $fieldMetadata = $metadata->fieldMappings[$field];

        $fieldType = $fieldMetadata['type'];
        return $fieldType;
    }
}

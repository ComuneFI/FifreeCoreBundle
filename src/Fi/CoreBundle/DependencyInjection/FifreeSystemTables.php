<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class FifreeSystemTables
{

    private $container;
    /* @var $em \Doctrine\ORM\EntityManager */
    private $em;
    private $entities = array();

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $this->container->get("doctrine")->getManager();
        $this->entities["Fi\CoreBundle\Entity\Ruoli"] = array("priority" => 10, "rows" => 0);
        $this->entities["Fi\CoreBundle\Entity\Operatori"] = array("priority" => 50, "rows" => 0);
        $this->entities["Fi\CoreBundle\Entity\Permessi"] = array("priority" => 100, "rows" => 0);
        $this->entities["Fi\CoreBundle\Entity\Storicomodifiche"] = array("priority" => 110, "rows" => 0);
        $this->entities["Fi\CoreBundle\Entity\Tabelle"] = array("priority" => 120, "rows" => 0);
        $this->entities["Fi\CoreBundle\Entity\OpzioniTabella"] = array("priority" => 150, "rows" => 0);
        $this->entities["Fi\CoreBundle\Entity\Ffprincipale"] = array("priority" => 160, "rows" => 0);
        $this->entities["Fi\CoreBundle\Entity\Ffsecondaria"] = array("priority" => 170, "rows" => 0);
        $this->entities["Fi\CoreBundle\Entity\MenuApplicazione"] = array("priority" => 180, "rows" => 0);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function countEntitiesRows()
    {
        $records = $this->entities;
        foreach ($records as $entity => $detail) {
            $qb = $this->em;
            $numrows = $qb->createQueryBuilder()
                    ->select('count(table.id)')
                    ->from($entity, 'table')
                    ->getQuery()
                    ->getSingleScalarResult();
            $this->entities[$entity]["rows"] = $numrows;
        }
    }

    public function dumpSystemEntities()
    {
        $this->countEntitiesRows();
        dump($this->entities);
    }

    public function getSystemEntities()
    {
        $this->countEntitiesRows();
        return $this->entities;
    }
}

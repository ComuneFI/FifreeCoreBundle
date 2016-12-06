<?php

namespace Fi\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class OpzioniTabellaRepository extends EntityRepository
{

    private static $namespace;
    private static $bundle;
    private static $controller;

    public function setup()
    {
        self::$namespace = "Fi";
        self::$bundle = "Core";
        self::$controller = "Tabelle";
    }

    public function editTestataFormTabelle($testatagriglia, $controller, $container)
    {
        $em = $container->get('doctrine')->getManager();
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('a'));
        $qb->from('FiCoreBundle:OpzioniTabella', 'a');
        $qb->leftJoin('a.tabelle', 't');
        $qb->where('t.nometabella = :tabella');
        $qb->andWhere("t.nomecampo is null or t.nomecampo = ''");
        $qb->setParameter('tabella', $controller);
        $opzioni = $qb->getQuery()->getResult();
        foreach ($opzioni as $opzione) {
            $testatagriglia[$opzione->getParametro()] = $opzione->getValore();
        }

        $testata = json_encode($testatagriglia);

        return $testata;
    }
}

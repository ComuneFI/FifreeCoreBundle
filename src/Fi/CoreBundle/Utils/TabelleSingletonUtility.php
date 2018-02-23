<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fi\CoreBundle\Utils;

final class TabelleSingletonUtility
{

    private static $queryTabelle;

    /**
     * Call this method to get singleton
     *
     * @return UserFactory
     */
    public static function instance($em, $nometabella, $operatore)
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new TabelleSingletonUtility($em, $nometabella, $operatore);
        }
        return $inst;
    }

    /**
     * Private construct so nobody else can instantiate it
     *
     */
    private function __construct($em, $nometabella, $operatore)
    {
        self::$queryTabelle = $em->getRepository('FiCoreBundle:Tabelle')->findBy(array('operatori_id' => $operatore, 'nometabella' => $nometabella));

        if (!self::$queryTabelle) {
            self::$queryTabelle = $em->getRepository('FiCoreBundle:Tabelle')->findBy(array('operatori_id' => null, 'nometabella' => $nometabella));
        }
    }

    public static function getTabelle()
    {
        return self::$queryTabelle;
    }
}

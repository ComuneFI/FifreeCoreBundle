<?php

namespace Fi\CoreBundle\Utils;

final class TabelleSingletonUtility
{

    private static $queryTabelle;
    private static $nometabella;
    private static $operatore;

    /**
     * Call this method to get singleton
     *
     * @return \Fi\CoreBundle\Entity\Tabelle
     */
    public static function instance($em, $nometabella, $operatore)
    {
        static $inst = null;
        if ($inst === null) {
            self::$nometabella = $nometabella;
            self::$operatore = $operatore;
            $inst = new TabelleSingletonUtility($em, $nometabella, $operatore);
        }
        if ($nometabella != self::$nometabella || $operatore != self::$operatore) {
            self::$nometabella = $nometabella;
            self::$operatore = $operatore;
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

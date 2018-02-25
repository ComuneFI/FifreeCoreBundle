<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fi\CoreBundle\Utils;

final class PermessiSingletonUtility
{

    private static $PermessiTabelle;
    private static $modulo;
    private static $operatore;
    private static $ruolo;

    public static function instance($em, $modulo, $operatore, $ruolo)
    {
        static $inst = null;
        if ($inst === null) {
            self::$modulo = $modulo;
            self::$operatore = $operatore;
            self::$ruolo = $ruolo;
            $inst = new PermessiSingletonUtility($em, $modulo, $operatore, $ruolo);
        }
        if ($modulo != self::$modulo || $operatore != self::$operatore || $ruolo != self::$ruolo) {
            self::$modulo = $modulo;
            self::$operatore = $operatore;
            self::$ruolo = $ruolo;
            $inst = new PermessiSingletonUtility($em, $modulo, $operatore, $ruolo);
        }
        return $inst;
    }

    /**
     * Private construct so nobody else can instantiate it
     *
     */
    private function __construct($em, $modulo, $operatore, $ruolo)
    {
        $q = $em
                ->getRepository('FiCoreBundle:Permessi')
                ->findOneBy(array('operatori_id' => $operatore, 'modulo' => $modulo));

        if (!$q) {
            $q = $em
                    ->getRepository('FiCoreBundle:Permessi')
                    ->findOneBy(array('ruoli_id' => $ruolo, 'modulo' => $modulo, 'operatori_id' => null));
            if (!$q) {
                $q = $em
                        ->getRepository('FiCoreBundle:Permessi')
                        ->findOneBy(array('ruoli_id' => null, 'modulo' => $modulo, 'operatori_id' => null));
            }
        }

        self::$PermessiTabelle = $q;
    }

    /**
     * Call this method to get singleton
     *
     * @return \Fi\CoreBundle\Entity\Permessi
     */
    public static function getPermessi()
    {
        return self::$PermessiTabelle;
    }
}

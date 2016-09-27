<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaUtils {

    public static $decodificaop;
    public static $precarattere;
    public static $postcarattere;

    public static function init() {
        // i possibili operatori di ciascuna ricerca sono questi:
        //['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc', 'nu', 'nn']
        //significano questo
        //['equal','not equal', 'less', 'less or equal','greater','greater or equal', 'begins with','does not begin with','is in','is not in','ends with','does not end with','contains','does not contain', 'is null', 'is not null']
        // sulla base dell'operatore impostato per la singola ricerca si impostano tre vettori
        // il promo contiene l'operatore da usare in query
        self::$decodificaop = array('eq' => '=', 'ne' => '<>', 'lt' => '<', 'le' => '<=', 'gt' => '>', 'ge' => '>=', 'bw' => 'LIKE', 'bn' => 'NOT LIKE', 'in' => 'IN', 'ni' => 'NOT IN', 'ew' => 'LIKE', 'en' => 'NOT LIKE', 'cn' => 'LIKE', 'nc' => 'NOT LIKE', 'nu' => 'IS', 'nn' => 'IS NOT', 'nt' => '<>');
        // questo contiene il carattere da usare prima del campo dati in query dipendentemente dal tipo di operatore
        self::$precarattere = array('eq' => '', 'ne' => '', 'lt' => '', 'le' => '', 'gt' => '', 'ge' => '', 'bw' => 'lower(\'', 'bn' => 'lower(\'', 'in' => '(', 'ni' => '(', 'ew' => 'lower(\'%', 'en' => 'lower(\'%', 'cn' => 'lower(\'%', 'nc' => 'lower(\'%', 'nu' => 'NULL', 'nn' => 'NULL', 'nt' => 'TRUE');
        // questo contiene il carattere da usare dopo il campo dati in query dipendentemente dal tipo di operatore
        self::$postcarattere = array('eq' => '', 'ne' => '', 'lt' => '', 'le' => '', 'gt' => '', 'ge' => '', 'bw' => '%\')', 'bn' => '%\')', 'in' => ')', 'ni' => ')', 'ew' => '\')', 'en' => '\')', 'cn' => '%\')', 'nc' => '%\')', 'nu' => '', 'nn' => '', 'nt' => '');
    }

    public static function setVettoriPerData() {
        self::$precarattere['eq'] = "'";
        self::$precarattere['ne'] = "'";
        self::$precarattere['lt'] = "'";
        self::$precarattere['le'] = "'";
        self::$precarattere['gt'] = "'";
        self::$precarattere['ge'] = "'";
        self::$postcarattere['eq'] = "'";
        self::$postcarattere['ne'] = "'";
        self::$postcarattere['lt'] = "'";
        self::$postcarattere['le'] = "'";
        self::$postcarattere['gt'] = "'";
        self::$postcarattere['ge'] = "'";
    }

    public static function setVettoriPerStringa() {
        self::$precarattere['eq'] = "lower('";
        self::$precarattere['ne'] = "lower('";
        self::$precarattere['lt'] = "lower('";
        self::$precarattere['le'] = "lower('";
        self::$precarattere['gt'] = "lower('";
        self::$precarattere['ge'] = "lower('";
        self::$postcarattere['eq'] = "')";
        self::$postcarattere['ne'] = "')";
        self::$postcarattere['lt'] = "')";
        self::$postcarattere['le'] = "')";
        self::$postcarattere['gt'] = "')";
        self::$postcarattere['ge'] = "')";
    }

    public static function setVettoriPerNumero() {
        self::$precarattere['eq'] = '';
        self::$precarattere['ne'] = '';
        self::$precarattere['lt'] = '';
        self::$precarattere['le'] = '';
        self::$precarattere['gt'] = '';
        self::$precarattere['ge'] = '';
        self::$postcarattere['eq'] = '';
        self::$postcarattere['ne'] = '';
        self::$postcarattere['lt'] = '';
        self::$postcarattere['le'] = '';
        self::$postcarattere['gt'] = '';
        self::$postcarattere['ge'] = '';
    }

    public static function getOuputType($parametri) {
        if ((isset($parametri['output'])) && ($parametri['output'] == 'stampa')) {
            $output = 'stampa';
        } else {
            $output = 'index';
        }
        return $output;
    }

    public static function getDoctrineByEm($parametri) {
        if (isset($parametri['em'])) {
            $doctrine = $parametri['container']->get('doctrine')->getManager($parametri['em']);
        } else {
            $doctrine = $parametri['container']->get('doctrine')->getManager();
        }
        return $doctrine;
    }

    public static function getDoctrineFiCoreByEm($parametri, $doctrine) {
        if (isset($parametri['emficore'])) {
            $doctrineficore = $parametri['container']->get('doctrine')->getManager($parametri['emficore']);
        } else {
            $doctrineficore = &$doctrine;
        }
        return $doctrineficore;
    }

    public static function getCampiEsclusi($riga, $output) {
        $campoescluso = null;
        if ($output == 'stampa') {
            if ($riga->hasMostrastampa() == false) {
                $campoescluso = $riga->getNomecampo();
            }
        } else {
            if ($riga->hasMostraindex() == false) {
                $campoescluso = $riga->getNomecampo();
            }
        }
        return $campoescluso;
    }

}

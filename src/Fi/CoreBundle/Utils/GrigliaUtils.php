<?php

namespace Fi\CoreBundle\Utils;

class GrigliaUtils
{

    public static $decodificaop;
    public static $precarattere;
    public static $postcarattere;
    public static $precaratterecampo;
    public static $postcaratterecampo;

    const LARGHEZZAMASSIMA = 500;
    const MOLTIPLICATORELARGHEZZA = 10;

    public static function init()
    {
        // i possibili operatori di ciascuna ricerca sono
        //questi: ['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc', 'nu', 'nn']
        //significano questo
        //['equal','not equal', 'less', 'less or equal','greater','greater or equal',
        //'begins with','does not begin with','is in','is not in','ends with','does not end with',
        //'contains','does not contain', 'is null', 'is not null']
        //sulla base dell'operatore impostato per la singola ricerca si impostano tre vettori
        //il promo contiene l'operatore da usare in query

        self::$decodificaop = array(
            'eq' => '=',
            'ne' => '<>',
            'lt' => '<',
            'le' => '<=',
            'gt' => '>',
            'ge' => '>=',
            'bw' => 'LIKE',
            'bn' => 'NOT LIKE',
            'in' => 'IN',
            'ni' => 'NOT IN',
            'ew' => 'LIKE',
            'en' => 'NOT LIKE',
            'cn' => 'LIKE',
            'nc' => 'NOT LIKE',
            'nu' => 'IS',
            'nn' => 'IS NOT',
            'nt' => '<>'
        );
        // questo contiene il carattere da usare prima del campo dati in query dipendentemente dal tipo di operatore
        self::$precarattere = array(
            'eq' => '',
            'ne' => '',
            'lt' => '',
            'le' => '',
            'gt' => '',
            'ge' => '',
            'bw' => 'lower(\'',
            'bn' => 'lower(\'',
            'in' => '(',
            'ni' => '(',
            'ew' => 'lower(\'%',
            'en' => 'lower(\'%',
            'cn' => 'lower(\'%',
            'nc' => 'lower(\'%',
            'nu' => '',
            'nn' => '',
            'nt' => 'TRUE'
        );

        // questo contiene il carattere da usare dopo il campo dati in query dipendentemente dal tipo di operatore
        self::$postcarattere = array(
            'eq' => '',
            'ne' => '',
            'lt' => '',
            'le' => '',
            'gt' => '',
            'ge' => '',
            'bw' => '%\')',
            'bn' => '%\')',
            'in' => ')',
            'ni' => ')',
            'ew' => '\')',
            'en' => '\')',
            'cn' => '%\')',
            'nc' => '%\')',
            'nu' => '',
            'nn' => '',
            'nt' => '');

        // questo contiene il carattere da usare prima del campo dati in query dipendentemente dal tipo di operatore
        self::$precaratterecampo = array(
            'eq' => '',
            'ne' => '',
            'lt' => '',
            'le' => '',
            'gt' => '',
            'ge' => '',
            'bw' => 'lower(',
            'bn' => 'lower(',
            'in' => '(',
            'ni' => '(',
            'ew' => 'lower(',
            'en' => 'lower(',
            'cn' => 'lower(',
            'nc' => 'lower(',
            'nu' => '',
            'nn' => '',
            'nt' => 'TRUE',
        );

        // questo contiene il carattere da usare dopo il campo dati in query dipendentemente dal tipo di operatore
        self::$postcaratterecampo = array(
            'eq' => '',
            'ne' => '',
            'lt' => '',
            'le' => '',
            'gt' => '',
            'ge' => '',
            'bw' => ')',
            'bn' => ')',
            'in' => ')',
            'ni' => ')',
            'ew' => ')',
            'en' => ')',
            'cn' => ')',
            'nc' => ')',
            'nu' => '',
            'nn' => '',
            'nt' => '',);
    }

    public static function setVettoriPerData()
    {
        self::$precaratterecampo['eq'] = "";
        self::$postcaratterecampo['eq'] = "";
        self::$precaratterecampo['ne'] = "";
        self::$postcaratterecampo['ne'] = "";

        self::$precarattere['eq'] = "";
        self::$precarattere['ne'] = "";
        self::$precarattere['lt'] = "";
        self::$precarattere['le'] = "";
        self::$precarattere['gt'] = "";
        self::$precarattere['ge'] = "";
        self::$postcarattere['eq'] = "";
        self::$postcarattere['ne'] = "";
        self::$postcarattere['lt'] = "";
        self::$postcarattere['le'] = "";
        self::$postcarattere['gt'] = "";
        self::$postcarattere['ge'] = "";
    }

    public static function setVettoriPerBoolean()
    {
        self::setVettoriPerData();
    }

    public static function setVettoriPerStringa()
    {
        self::$precaratterecampo['eq'] = "lower(";
        self::$postcaratterecampo['eq'] = ")";
        self::$precaratterecampo['ne'] = "lower(";
        self::$postcaratterecampo['ne'] = ")";

        self::$precarattere['eq'] = "";
        self::$precarattere['ne'] = "";
        self::$precarattere['lt'] = "";
        self::$precarattere['le'] = "";
        self::$precarattere['gt'] = "";
        self::$precarattere['ge'] = "";
        self::$precarattere['cn'] = "%";
        self::$precarattere['nc'] = "%";
        self::$precarattere['bw'] = "";
        self::$precarattere['in'] = "lower(";
        self::$precarattere['ni'] = "lower(";
        self::$postcarattere['eq'] = "";
        self::$postcarattere['ne'] = "";
        self::$postcarattere['lt'] = "";
        self::$postcarattere['le'] = "";
        self::$postcarattere['gt'] = "";
        self::$postcarattere['ge'] = "";
        self::$postcarattere['cn'] = "%";
        self::$postcarattere['nc'] = "%";
        self::$postcarattere['bw'] = "%";
        self::$postcarattere['in'] = ")";
        self::$postcarattere['ni'] = ")";
    }

    public static function setVettoriPerNumero()
    {
        self::$precaratterecampo['eq'] = "";
        self::$postcaratterecampo['eq'] = "";
        self::$precaratterecampo['ne'] = "";
        self::$postcaratterecampo['ne'] = "";
        self::$precaratterecampo['in'] = "(";
        self::$postcaratterecampo['in'] = ")";
        self::$precaratterecampo['ni'] = "(";
        self::$postcaratterecampo['ni'] = ")";

        self::$precarattere['eq'] = '';
        self::$precarattere['ne'] = '';
        self::$precarattere['lt'] = '';
        self::$precarattere['le'] = '';
        self::$precarattere['gt'] = '';
        self::$precarattere['ge'] = '';
        self::$precarattere['in'] = '';
        self::$precarattere['ni'] = '';
        self::$postcarattere['eq'] = '';
        self::$postcarattere['ne'] = '';
        self::$postcarattere['lt'] = '';
        self::$postcarattere['le'] = '';
        self::$postcarattere['gt'] = '';
        self::$postcarattere['ge'] = '';
        self::$postcarattere['in'] = '';
        self::$postcarattere['ni'] = '';
    }

    public static function getCampiEsclusi($riga, $output)
    {
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

    public static function getUserCustomTableFields($em, $nometabella, $operatore)
    {
        return TabelleSingletonUtility::instance($em, $nometabella, $operatore)->getTabelle();
    }

    public static function etichettecampi($parametri = array())
    {
        if (!isset($parametri['nometabella'])) {
            return false;
        }

        $output = GrigliaParametriUtils::getOuputType($parametri);

        $nometabella = $parametri['nometabella'];

        $doctrine = GrigliaParametriUtils::getDoctrineByEm($parametri);
        $doctrineficore = GrigliaParametriUtils::getDoctrineFiCoreByEm($parametri, $doctrine);
        $container = $parametri['container'];
        $gestionepermessi = $container->get("ficorebundle.gestionepermessi");

        $operatorecorrente = $gestionepermessi->utentecorrente();

        $etichette = array();

        $q = self::getUserCustomTableFields($doctrineficore, $nometabella, $operatorecorrente["id"]);

        if ($q) {
            foreach ($q as $riga) {
                if ($output == 'stampa') {
                    $etichette[$riga->getNomecampo()] = $riga->getEtichettastampa();
                } else {
                    $etichette[$riga->getNomecampo()] = $riga->getEtichettaindex();
                }
            }
        }

        return $etichette;
    }

    public static function larghezzecampi($parametri = array())
    {
        if (!isset($parametri['nometabella'])) {
            return false;
        }

        $output = GrigliaParametriUtils::getOuputType($parametri);

        $nometabella = $parametri['nometabella'];

        $doctrine = GrigliaParametriUtils::getDoctrineByEm($parametri);
        $doctrineficore = GrigliaParametriUtils::getDoctrineFiCoreByEm($parametri, $doctrine);

        $container = $parametri['container'];
        $gestionepermessi = $container->get("ficorebundle.gestionepermessi");
        $operatorecorrente = $gestionepermessi->utentecorrente();

        $etichette = array();

        $q = self::getUserCustomTableFields($doctrineficore, $nometabella, $operatorecorrente["id"]);

        if (!$q) {
            return $etichette;
        }

        foreach ($q as $riga) {
            if ($output == 'stampa') {
                $etichette[$riga->getNomecampo()] = $riga->getLarghezzastampa();
            } else {
                $etichette[$riga->getNomecampo()] = $riga->getLarghezzaindex();
            }
        }

        return $etichette;
    }

    public static function ordinecolonne($parametri = array())
    {
        if (!isset($parametri['nometabella'])) {
            return false;
        }

        $output = GrigliaParametriUtils::getOuputType($parametri);

        $nometabella = $parametri['nometabella'];

        $doctrine = GrigliaParametriUtils::getDoctrineByEm($parametri);
        $doctrineficore = GrigliaParametriUtils::getDoctrineFiCoreByEm($parametri, $doctrine);
        $container = $parametri['container'];

        $gestionepermessi = $container->get("ficorebundle.gestionepermessi");

        $operatorecorrente = $gestionepermessi->utentecorrente();

        $ordine = array();

        $q = self::getUserCustomTableFields($doctrineficore, $nometabella, $operatorecorrente["id"]);

        if ($q) {
            foreach ($q as $riga) {
                if ($output == 'stampa') {
                    if ($riga->getOrdinestampa()) {
                        $ordine[$riga->getOrdinestampa()] = $riga->getNomecampo();
                    }
                } else {
                    if ($riga->getOrdineindex()) {
                        $ordine[$riga->getOrdineindex()] = $riga->getNomecampo();
                    }
                }
            }
            if (count($ordine) > 0) {
                ksort($ordine);
            }
        }

        $ordinecolonne = GrigliaColonneUtils::getColonneOrdinate($ordine);

        return $ordinecolonne;
    }

    /**
     * Translates a string with underscores into camel case (e.g. first_name -&gt; firstName).
     *
     * @param array  $parametri
     *
     * @return string $str translated into camel caps
     */
    public static function toCamelCase($parametri = array())
    {
        $str = $parametri['str'];
        $capitalise_first_char = isset($parametri['primamaiuscola']) ? $parametri['primamaiuscola'] : false;

        if ($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        $func = function ($matches) {
            return strtoupper($matches[1]);
        };

        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    public static function getOpzioniTabella($doctrineficore, $nometabella, &$testata)
    {
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $doctrineficore->createQueryBuilder();
        $qb->select(array('a'));
        $qb->from('FiCoreBundle:OpzioniTabella', 'a');
        $qb->leftJoin('a.tabelle', 't');
        $qb->where("t.nometabella = '*' or t.nometabella = :tabella");
        $qb->andWhere("t.nomecampo is null or t.nomecampo = ''");
        $qb->orderBy('t.nometabella');
        $qb->setParameter('tabella', $nometabella);
        $opzioni = $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getResult();
        foreach ($opzioni as $opzione) {
            $testata[$opzione->getParametro()] = str_replace('%tabella%', $nometabella, $opzione->getValore());
        }
    }

    public static function getNomiColonne($nomicolonne)
    {
        ksort($nomicolonne);
        $nomicolonnesorted = array();
        foreach ($nomicolonne as $value) {
            $nomicolonnesorted[] = $value;
        }

        return $nomicolonnesorted;
    }

    public static function getModelloColonne($modellocolonne)
    {
        ksort($modellocolonne);
        $modellocolonnesorted = array();
        foreach ($modellocolonne as $value) {
            $modellocolonnesorted[] = $value;
        }

        return $modellocolonnesorted;
    }

    public static function getPermessiTabella($paricevuti, &$testata)
    {
        if (!isset($paricevuti['container'])) {
            return;
        }

        $container = $paricevuti['container'];
        $nometabella = $paricevuti['nometabella'];
        $gestionepermessi = $container->get("ficorebundle.gestionepermessi");

        $vettorepermessi = $gestionepermessi->impostaPermessi(array('modulo' => $nometabella));

        return array_merge($testata, $vettorepermessi);
    }
}

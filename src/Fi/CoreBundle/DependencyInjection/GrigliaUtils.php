<?php

namespace Fi\CoreBundle\DependencyInjection;

use Fi\CoreBundle\Controller\GestionepermessiController;
use Fi\CoreBundle\Controller\FiUtilita;

class GrigliaUtils {

    public static $decodificaop;
    public static $precarattere;
    public static $postcarattere;

    const LARGHEZZAMASSIMA = 500;
    const MOLTIPLICATORELARGHEZZA = 10;

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

    public static function getUserCustomTableFields($em, $nometabella, $operatore) {
        $q = $em->getRepository('FiCoreBundle:tabelle')->findBy(array('operatori_id' => $operatore['id'], 'nometabella' => $nometabella));

        if (!$q) {
            unset($q);
            $q = $em->getRepository('FiCoreBundle:tabelle')->findBy(array('operatori_id' => null, 'nometabella' => $nometabella));
        }
        return $q;
    }

    public static function etichettecampi($parametri = array()) {
        if (!isset($parametri['nometabella'])) {
            return false;
        }

        $output = GrigliaUtils::getOuputType($parametri);

        $nometabella = $parametri['nometabella'];

        $doctrine = GrigliaUtils::getDoctrineByEm($parametri);
        $doctrineficore = GrigliaUtils::getDoctrineFiCoreByEm($parametri, $doctrine);

        $gestionepermessi = new GestionepermessiController($parametri['container']);
        $operatorecorrente = $gestionepermessi->utentecorrenteAction();

        $etichette = array();

        $q = GrigliaUtils::getUserCustomTableFields($doctrineficore, $nometabella, $operatorecorrente);

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

    public static function larghezzecampi($parametri = array()) {
        if (!isset($parametri['nometabella'])) {
            return false;
        }

        $output = GrigliaUtils::getOuputType($parametri);

        $nometabella = $parametri['nometabella'];

        $doctrine = GrigliaUtils::getDoctrineByEm($parametri);
        $doctrineficore = GrigliaUtils::getDoctrineFiCoreByEm($parametri, $doctrine);

        $gestionepermessi = new GestionepermessiController($parametri['container']);
        $operatorecorrente = $gestionepermessi->utentecorrenteAction();

        $etichette = array();

        $q = GrigliaUtils::getUserCustomTableFields($doctrineficore, $nometabella, $operatorecorrente);

        if ($q) {
            foreach ($q as $riga) {
                if ($output == 'stampa') {
                    $etichette[$riga->getNomecampo()] = $riga->getLarghezzastampa();
                } else {
                    $etichette[$riga->getNomecampo()] = $riga->getLarghezzaindex();
                }
            }
        }

        return $etichette;
    }

    public static function ordinecolonne($parametri = array()) {
        if (!isset($parametri['nometabella'])) {
            return false;
        }

        $output = GrigliaUtils::getOuputType($parametri);

        $nometabella = $parametri['nometabella'];

        $doctrine = GrigliaUtils::getDoctrineByEm($parametri);
        $doctrineficore = GrigliaUtils::getDoctrineFiCoreByEm($parametri, $doctrine);

        $gestionepermessi = new GestionepermessiController($parametri['container']);
        $operatorecorrente = $gestionepermessi->utentecorrenteAction();

        $ordine = array();

        $q = GrigliaUtils::getUserCustomTableFields($doctrineficore, $nometabella, $operatorecorrente);

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

        $ordinecolonne = self::getOrdineColonne($ordine);

        return $ordinecolonne;
    }

    public static function getOrdineColonne($ordine) {
        $ordinecolonne = null;

        if (count($ordine) > 0) {
            $ordinecolonne = array();
            foreach ($ordine as $value) {
                $ordinecolonne[] = $value;
            }
        }
        return $ordinecolonne;
    }

    private static function getCampiExtraNormalizzati(&$campiextra) {
        //Se è un array di una dimensione si trasforma in bidimensionale
        if (count($campiextra) == count($campiextra, \COUNT_RECURSIVE)) {
            $campoextraarray = $campiextra;
            $campiextra = array();
            foreach ($campoextraarray as $campoextranormalize) {
                if (is_object($campoextranormalize)) {
                    $campoextranormalize = get_object_vars($campoextranormalize);
                    $campiextra[] = $campoextranormalize;
                }
            }
        }
    }

    public static function getCampiExtraTestataPerGriglia($paricevuti, &$indice, &$nomicolonne, &$modellocolonne) {
        $campiextra = GrigliaParametriUtils::getParametriCampiExtraTestataPerGriglia($paricevuti);
        if (!isset($campiextra)) {
            return;
        }
        self::getCampiExtraNormalizzati($campiextra);
        
        foreach ($campiextra as $chiave => $colonna) {
            ++$indice;
            if (is_object($colonna)) {
                $colonna = get_object_vars($colonna);
            }
            
            $nomicolonne[$indice] = isset($colonna['descrizione']) ? $colonna['descrizione'] : GrigliaUtils::to_camel_case(array('str' => $chiave, 'primamaiuscola' => true));
            
            $widthcolonna = isset($colonna['lunghezza']) ? $colonna['lunghezza'] : ($colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA > GrigliaUtils::LARGHEZZAMASSIMA ? GrigliaUtils::LARGHEZZAMASSIMA : $colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA);

            $tipocolonna = isset($colonna['tipo']) ? $colonna['tipo'] : $colonna['type'];
            $idcolonna = isset($colonna['nomecampo']) ? $colonna['nomecampo'] : $chiave;
            $nomecolonna = isset($colonna['nomecampo']) ? $colonna['nomecampo'] : $chiave;

            $modellocolonne[$indice] = array(
                'name' => $nomecolonna,
                'id' => $idcolonna,
                'width' => $widthcolonna,
                'tipocampo' => $tipocolonna,
                'search' => false);
        }
    }

    /**
     * Translates a string with underscores into camel case (e.g. first_name -&gt; firstName).
     *
     * @param array  $parametri
     * @param string $str            String in underscore format
     * @param bool   $primamaiuscola If true, capitalise the first char in $str
     *
     * @return string $str translated into camel caps
     */
    public static function to_camel_case($parametri = array()) {
        $str = $parametri['str'];
        $capitalise_first_char = isset($parametri['primamaiuscola']) ? $parametri['primamaiuscola'] : false;

        if ($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');

        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    public static function getOpzioniTabella($doctrineficore, $nometabella, &$testata) {
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $doctrineficore->createQueryBuilder();
        $qb->select(array('a'));
        $qb->from('FiCoreBundle:OpzioniTabella', 'a');
        $qb->leftJoin('a.tabelle', 't');
        $qb->where("t.nometabella = '*' or t.nometabella = :tabella");
        $qb->andWhere("t.nomecampo is null or t.nomecampo = ''");
        $qb->orderBy('t.nometabella');
        $qb->setParameter('tabella', $nometabella);
        $opzioni = $qb->getQuery()->getResult();
        foreach ($opzioni as $opzione) {
            $testata[$opzione->getParametro()] = str_replace('%tabella%', $nometabella, $opzione->getValore());
        }
    }

    Public static function getNomiColonne($nomicolonne) {
        ksort($nomicolonne);
        $nomicolonnesorted = array();
        foreach ($nomicolonne as $value) {
            $nomicolonnesorted[] = $value;
        }
        return $nomicolonnesorted;
    }

    Public static function getModelloColonne($modellocolonne) {
        ksort($modellocolonne);
        $modellocolonnesorted = array();
        foreach ($modellocolonne as $value) {
            $modellocolonnesorted[] = $value;
        }
        return $modellocolonnesorted;
    }

    Public static function getPermessiTabella($paricevuti, &$testata) {
        if (!isset($paricevuti['container'])) {
            return;
        }

        $container = $paricevuti['container'];
        $nometabella = $paricevuti['nometabella'];
        $permessi = new GestionepermessiController();
        $permessi->setContainer($container);

        $vettorepermessi = $permessi->impostaPermessi(array('modulo' => $nometabella));
        return array_merge($testata, $vettorepermessi);
    }

}

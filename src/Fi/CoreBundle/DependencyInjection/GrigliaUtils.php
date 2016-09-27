<?php

namespace Fi\CoreBundle\DependencyInjection;

use Fi\CoreBundle\Controller\GestionepermessiController;
use Fi\CoreBundle\Controller\FiUtilita;

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

    public static function getAliasTestataPerGriglia($paricevuti) {
        $alias = isset($paricevuti['dettaglij']) ? $paricevuti['dettaglij'] : array();

        if (is_object($alias)) {
            $alias = get_object_vars($alias);
        }
        return $alias;
    }

    public static function getOrdineColonneTestataPerGriglia($paricevuti) {
        $ordinecolonne = isset($paricevuti['ordinecolonne']) ? $paricevuti['ordinecolonne'] : null;
        if (!isset($ordinecolonne)) {
            $ordinecolonne = GrigliaUtils::ordinecolonne($paricevuti);
        }
        return $ordinecolonne;
    }

    public static function getCampiEsclusiTestataPerGriglia($paricevuti) {
        return isset($paricevuti['escludere']) ? $paricevuti['escludere'] : null;
    }

    public static function getCampiExtraTestataPerGriglia($paricevuti) {
        return isset($paricevuti['campiextra']) ? $paricevuti['campiextra'] : null;
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

    public static function getTipoRegola($regola, $parametri) {
        $doctrine = $parametri['doctrine'];
        $nometabella = $parametri['nometabella'];
        $entityName = $parametri['entityName'];
        $bundle = $parametri['bundle'];

        $elencocampi = $doctrine->getClassMetadata($entityName)->getFieldNames();
        $tipo = null;
        if (strrpos($regola['field'], '.') == 0) {
            if (in_array($regola['field'], $elencocampi) == true) {
                $type = $doctrine->getClassMetadata($entityName)->getFieldMapping($regola['field']);
                $tipo = $type['type'];

                //Si aggiunge l'alias al campo altrimenti da Doctrine2 fallisce la query
                $regola['field'] = $nometabella . '.' . $regola['field'];
            }
        } else {
            //Altrimenti stiamo analizzando il campo di una tabella in leftjoin pertanto si cercano le informazioni sul tipo
            //dei campi nella tabella "joinata"
            $tablejoined = substr($regola['field'], 0, strrpos($regola['field'], '.'));
            $fieldjoined = substr($regola['field'], strrpos($regola['field'], '.') + 1);

            $entityNametablejoined = $bundle . ':' . $tablejoined;

            $type = $doctrine->getClassMetadata($entityNametablejoined)->getFieldMapping($fieldjoined);
            $tipo = $type['type'];
        }
        return $tipo;
    }

    public static function getRegolaPerData($regola) {
        if ((substr($regola['data'], 0, 1) == "'") && (substr($regola['data'], strlen($regola['data']) - 1, 1) == "'")) {
            $regola['data'] = substr($regola['data'], 1, strlen($regola['data']) - 2);
        }

        return $regola;
    }

    public static function setRegole(&$q, &$primo, $parametri = array()) {
        $regole = $parametri['regole'];
        $tipof = $parametri['tipof'];

        foreach ($regole as $regola) {
            //Se il campo non ha il . significa che è necessario aggiungere il nometabella
            $tipo = GrigliaUtils::getTipoRegola($regola, $parametri);

            $regola = self::setSingolaRegola($tipo, $regola);
            if (!$regola) {
                continue;
            }

            if ($tipof == 'OR') {
                $q->orWhere($regola['field'] . ' ' . GrigliaUtils::$decodificaop[$regola['op']] . ' ' . GrigliaUtils::$precarattere[$regola['op']] . $regola['data'] . GrigliaUtils::$postcarattere[$regola['op']]);
            } else {
                $q->andWhere($regola['field'] . ' ' . GrigliaUtils::$decodificaop[$regola['op']] . ' ' . GrigliaUtils::$precarattere[$regola['op']] . $regola['data'] . GrigliaUtils::$postcarattere[$regola['op']]);
            }
        }
    }

    public static function setSingolaRegola($tipo, $regola) {
        if (!$tipo) {
            GrigliaUtils::setVettoriPerNumero();
        } else {
            if ($tipo == 'date' || $tipo == 'datetime') {
                GrigliaUtils::setVettoriPerData();
                $regola['data'] = FiUtilita::data2db($regola['data']);
            } elseif ($tipo == 'string') {
                GrigliaUtils::setVettoriPerStringa();
                $regola['field'] = 'lower(' . $regola['field'] . ')';
            }

            if (($tipo == 'boolean') && ($regola['data'] === 'false' || $regola['data'] === false)) {
                $regola['op'] = 'eq';
                $regola['data'] = 0;
            }
            if (($tipo == 'boolean') && ($regola['data'] === 'true' || $regola['data'] === true)) {
                $regola['op'] = 'eq';
                $regola['data'] = 1;
            }
        }
        $regolareturn = GrigliaUtils::getRegolaPerData($regola);
        return $regolareturn;
    }

}

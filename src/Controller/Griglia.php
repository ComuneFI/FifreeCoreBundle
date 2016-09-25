<?php

namespace Fi\CoreBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;

class Griglia extends FiController {

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

    public static function campiesclusi($parametri = array()) {
        if (!isset($parametri['nometabella'])) {
            return false;
        }

        $output = self::getOuputType($parametri);

        $nometabella = $parametri['nometabella'];

        $doctrine = self::getDoctrineByEm($parametri);
        $doctrineficore = self::getDoctrineFiCoreByEm($parametri, $doctrine);

        $gestionepermessi = new GestionepermessiController($parametri['container']);
        $operatorecorrente = $gestionepermessi->utentecorrenteAction();

        $escludi = array();
        $q = self::getUserCustomTableFields($doctrineficore, $nometabella, $operatorecorrente);
        if (!$q) {
            return $escludi;
        }

        foreach ($q as $riga) {
            $campo = self::getCampiEsclusi($riga, $output);
            if ($campo) {
                $escludi[] = $campo;
            }
        }

        return $escludi;
    }

    private static function getUserCustomTableFields($em, $nometabella, $operatore) {
        $q = $em->getRepository('FiCoreBundle:tabelle')->findBy(array('operatori_id' => $operatore['id'], 'nometabella' => $nometabella));

        if (!$q) {
            unset($q);
            $q = $em->getRepository('FiCoreBundle:tabelle')->findBy(array('operatori_id' => null, 'nometabella' => $nometabella));
        }
        return $q;
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

    public static function etichettecampi($parametri = array()) {
        if (!isset($parametri['nometabella'])) {
            return false;
        }

        $output = self::getOuputType($parametri);

        $nometabella = $parametri['nometabella'];

        $doctrine = self::getDoctrineByEm($parametri);
        $doctrineficore = self::getDoctrineFiCoreByEm($parametri, $doctrine);

        $gestionepermessi = new GestionepermessiController($parametri['container']);
        $operatorecorrente = $gestionepermessi->utentecorrenteAction();

        $etichette = array();

        $q = self::getUserCustomTableFields($doctrineficore, $nometabella, $operatorecorrente);

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

        $output = self::getOuputType($parametri);

        $nometabella = $parametri['nometabella'];

        $doctrine = self::getDoctrineByEm($parametri);
        $doctrineficore = self::getDoctrineFiCoreByEm($parametri, $doctrine);

        $gestionepermessi = new GestionepermessiController($parametri['container']);
        $operatorecorrente = $gestionepermessi->utentecorrenteAction();

        $etichette = array();

        $q = self::getUserCustomTableFields($doctrineficore, $nometabella, $operatorecorrente);

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

        $output = self::getOuputType($parametri);

        $nometabella = $parametri['nometabella'];

        $doctrine = self::getDoctrineByEm($parametri);
        $doctrineficore = self::getDoctrineFiCoreByEm($parametri, $doctrine);

        $gestionepermessi = new GestionepermessiController($parametri['container']);
        $operatorecorrente = $gestionepermessi->utentecorrenteAction();

        $ordine = array();

        $q = self::getUserCustomTableFields($doctrineficore, $nometabella, $operatorecorrente);

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

    private static function getOrdineColonne($ordine) {
        $ordinecolonne = null;

        if (count($ordine) > 0) {
            $ordinecolonne = array();
            foreach ($ordine as $value) {
                $ordinecolonne[] = $value;
            }
        }
        return $ordinecolonne;
    }

    private static function getTipoRegola($regola, $parametri) {
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

    public static function setSingolaRegola($tipo, $regola) {
        if (!$tipo) {
            self::setVettoriPerNumero();
        } else {
            if ($tipo == 'date' || $tipo == 'datetime') {
                self::setVettoriPerData();
                $regola['data'] = FiUtilita::data2db($regola['data']);
            } elseif ($tipo == 'string') {
                self::setVettoriPerStringa();
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
        $regolareturn = self::getRegolaPerData($regola);
        return $regolareturn;
    }

    private static function getRegolaPerData($regola) {
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
            $tipo = self::getTipoRegola($regola, $parametri);

            $regola = self::setSingolaRegola($tipo, $regola);
            if (!$regola) {
                continue;
            }

            if ($tipof == 'OR') {
                $q->orWhere($regola['field'] . ' ' . self::$decodificaop[$regola['op']] . ' ' . self::$precarattere[$regola['op']] . $regola['data'] . self::$postcarattere[$regola['op']]);
            } else {
                $q->andWhere($regola['field'] . ' ' . self::$decodificaop[$regola['op']] . ' ' . self::$precarattere[$regola['op']] . $regola['data'] . self::$postcarattere[$regola['op']]);
            }
        }
    }

    public static function setTabelleJoin(&$q, $parametri = array()) {
        $tabellej = $parametri['tabellej'];
        $nometabella = $parametri['nometabella'];

        foreach ($tabellej as $tabellaj) {
            if (is_object($tabellaj)) {
                $tabellaj = get_object_vars($tabellaj);
            }
            //Serve per far venire nella getArrayResult() anche i campi della tabella il leftjoin
            //altrimenti mostra solo quelli della tabella con alias a
            $q->addSelect(array($tabellaj['tabella']));
            $q = $q->leftJoin((isset($tabellaj['padre']) ? $tabellaj['padre'] : $nometabella) . '.' . $tabellaj['tabella'], $tabellaj['tabella']);
        }
    }

    public static function setPrecondizioni(&$q, &$primo, $parametri = array()) {
        $precondizioni = $parametri['precondizioni'];

        //var_dump($precondizioni);

        $i = 1;
        foreach ($precondizioni as $nomecampopre => $precondizione) {
            if ($primo) {
                $q->where("$nomecampopre = :var$i");

                $primo = false;
            } else {
                $q->andWhere("$nomecampopre = :var$i");
            }
            $q->setParameter("var$i", $precondizione);
            ++$i;
        }
    }

    public static function setPrecondizioniAvanzate(&$q, &$primo, $parametri = array()) {
        $doctrine = $parametri['doctrine'];
        $nometabella = $parametri['nometabella'];
        $entityName = $parametri['entityName'];
        $bundle = $parametri['bundle'];
        $precondizioniAvanzate = $parametri['precondizioniAvanzate'];
        $regole = array();

        foreach ($precondizioniAvanzate as $elem) {
            $nometabellaprecondizione = '';
            $nomecampoprecondizione = '';
            $valorecampoprecondizione = '';
            $operatoreprecondizione = '=';
            $operatorelogicoprecondizione = '';
            foreach ($elem as $keypre => $valuepre) {
                if ($keypre == 'nometabella') {
                    $nometabellaprecondizione = $valuepre;
                } elseif ($keypre == 'nomecampo') {
                    $nomecampoprecondizione = $valuepre;
                } elseif ($keypre == 'operatore') {
                    $array_operatori = array('=' => 'eq', '<>' => 'ne', '<' => 'lt', '<=' => 'le', '>' => 'gt', '>=' => 'ge', 'LIKE' => 'bw', 'NOT LIKE' => 'bn', 'IN' => 'in', 'NOT IN' => 'ni', 'LIKE' => 'eq', 'NOT LIKE' => 'en', 'LIKE' => 'cn', 'NOT LIKE' => 'nc', 'IS' => 'nu', 'IS NOT' => 'nn'); //, '<>' => 'nt');
                    $operatoreprecondizione = $array_operatori[strtoupper($valuepre)];
                } elseif ($keypre == 'valorecampo') {
                    if (is_array($valuepre)) {
                        $type = $doctrine->getClassMetadata($parametri['entityName'])->getFieldMapping($nomecampoprecondizione);

                        $tipo = $type['type'];
                        if ($tipo && ($tipo == 'date' || $tipo == 'datetime')) {
                            self::setVettoriPerData();
                            foreach ($valuepre as $chiave => $valore) {
                                $valuepre[$chiave] = fiUtilita::data2db($valore);
                            }
                        } elseif ($tipo && $tipo == 'string') {
                            self::setVettoriPerStringa();
                            foreach ($valuepre as $chiave => $valore) {
                                $valuepre[$chiave] = strtolower($valore);
                            }
                        } else {
                            self::setVettoriPerNumero();
                        }
                        $valorecampoprecondizione = implode(', ', $valuepre); // se si tratta di valori numerici tutto ok, altrimenti non funziona
                    } else {
                        $valorecampoprecondizione = $valuepre;
                    }
                } elseif ($keypre == 'operatorelogico') {
                    $operatorelogicoprecondizione = strtoupper($valuepre);
                }
            }
            $regole[] = array('field' => "$nometabellaprecondizione.$nomecampoprecondizione", 'op' => $operatoreprecondizione, 'data' => $valorecampoprecondizione);
            $tipof = $operatorelogicoprecondizione;

            self::setRegole(
                    $q, $primo, array(
                'regole' => $regole,
                'doctrine' => $doctrine,
                'nometabella' => $nometabella,
                'entityName' => $entityName,
                'bundle' => $bundle,
                'tipof' => $tipof,
                    )
            );
            $primo = false;
        }
    }

    public static function getColonne($parametri = array()) {
        $entityName = $parametri['entityName'];
        /* @var $doctrine \Doctrine\ORM\EntityManager */
        $doctrine = $parametri['doctrine'];

        //$infocolonne = $doctrine->getClassMetadata($entityName)->getColumnNames();
        $infocolonne = $doctrine->getMetadataFactory()->getMetadataFor($entityName);
        //$infocolonne = get_object_vars($infocolonne);

        foreach ($infocolonne->fieldMappings as $colonna) {
            //getFieldMapping
            //$doctrine->getConnection()->getSchemaManager()->
            //$ret = $doctrine->getMetadataFactory()->getMetadataFor($entityName)->;
            //if ($colonna == 'descrizione' ){
            $colonne[$colonna['fieldName']] = $colonna;
            //}

            /* $colonne[$colonna] = $doctrine->getClassMetadata($entityName)->getTypeOfField($colonna);
              $colonne[$colonna] = $doctrine->getClassMetadata($entityName)->getColumnName($colonna);
              $colonne[$colonna] = $doctrine->getClassMetadata($entityName)->getFieldForColumn($colonna);
              $colonne[$colonna] = $doctrine->getClassMetadata($entityName)->getTypeOfColumn($colonna);
              $colonne[$colonna] = $doctrine->getClassMetadata($entityName)->getColumnNames();
             */
            if ($colonne[$colonna['fieldName']]['type'] == 'integer' || !(isset($colonne[$colonna['fieldName']]['length']))) {
                $colonne[$colonna['fieldName']]['length'] = 11;
            }
        }

        return $colonne;
    }

    /**
     * Questa funzione è compatibile con jqGrid e risponden con un formato JSON
     * contenente i dati di testata per la griglia.
     *
     * @param array  $paricevuti
     * @param object $paricevuti[request]      oggetto che contiene il POST passato alla griglia
     * @param string $paricevuti[nometabella]
     * @param array  $paricevuti[dettaglij]    array contenente tutte le tabelle per le quali richiedere
     *                                         la join a partire da $paricevuti[nometabella]
     *                                         il vettore è composto da array("nomecampodadecodificare"=>array("descrizione"=>"nometabella.campodecodifica", "lunghezza"=>"40"))
     * @param array  $paricevuti[colonne_link] array contenente eventuali colonne che debbano essere
     *                                         rappresentate da un link. Non è da confondere con i
     *                                         parametri_link di datiPerGriglia, perché QUESTO array
     *                                         si può passare alla testata se si vuole avere una
     *                                         colonna link che prenda in automatico
     *                                         parametro id = al valore dell'id della tabella
     *                                         principale su cui si sta facendo la griglia
     * @param string $paricevuti[output]       : "index" se la testata serve per la griglia dell'index, "stampa" se la testata serve per la stampa
     *
     * @return array contentente i dati di testata per la griglia
     */
    public static function testataPerGriglia($paricevuti = array()) {
        $nometabella = $paricevuti['nometabella'];
        $bundle = $paricevuti['nomebundle'];

        if ((isset($paricevuti['output'])) && ($paricevuti['output'] == 'stampa')) {
            $output = 'stampa';
        } else {
            $output = 'index';
        }

        $doctrine = self::getDoctrineByEm($paricevuti);
        $doctrineficore = self::getDoctrineFiCoreByEm($paricevuti, $doctrine);

        $alias = isset($paricevuti['dettaglij']) ? $paricevuti['dettaglij'] : array();

        if (is_object($alias)) {
            $alias = get_object_vars($alias);
        }

        $colonne_link = isset($paricevuti['colonne_link']) ? $paricevuti['colonne_link'] : array();

        $escludereutente = self::campiesclusi($paricevuti);
        $etichetteutente = self::etichettecampi($paricevuti);
        $larghezzeutente = self::larghezzecampi($paricevuti);

        $escludere = isset($paricevuti['escludere']) ? $paricevuti['escludere'] : null;

        $campiextra = isset($paricevuti['campiextra']) ? $paricevuti['campiextra'] : array();

        $ordinecolonne = isset($paricevuti['ordinecolonne']) ? $paricevuti['ordinecolonne'] : null;
        if (!isset($ordinecolonne)) {
            $ordinecolonne = self::ordinecolonne($paricevuti);
        }

        /* @var $em \Doctrine\ORM\EntityManager */
        //$em = $doctrine->getRepository($bundle . ":" . $nometabella)->findAll();
        $entityName = $bundle . ':' . $nometabella;

        $colonne = self::getColonne(array('entityName' => $entityName, 'doctrine' => $doctrine));

        $larghezzamassima = 500;
        $moltiplicatorelarghezza = 10;

        $testata = array();
        $nomicolonne = array();
        $modellocolonne = array();
        $indice = 0;

        foreach ($colonne as $chiave => $colonna) {
            if ((!isset($escludere) || !(in_array($chiave, $escludere))) && (!isset($escludereutente) || !(in_array($chiave, $escludereutente)))) {
                $moltialias = (isset($alias[$chiave]) ? $alias[$chiave] : null);

                if (isset($alias[$chiave])) {
                    foreach ($moltialias as $singoloalias) {
                        if (isset($ordinecolonne)) {
                            $indicecolonna = array_search($chiave, $ordinecolonne);
                            if ($indicecolonna === false) {
                                if ($indice === 0) {
                                    $indice = count($ordinecolonne);
                                }
                                ++$indice;
                                $indicecolonna = $indice;
                            } else {
                                if ($indicecolonna > $indice) {
                                    $indice = $indicecolonna;
                                }
                            }
                        } else {
                            ++$indice;
                            $indicecolonna = $indice;
                        }

                        if (is_object($singoloalias)) {
                            $singoloalias = get_object_vars($singoloalias);
                        }

                        if ((isset($etichetteutente[$chiave])) && (trim($etichetteutente[$chiave]) != '')) {
                            $nomicolonne[$indicecolonna] = self::to_camel_case(array('str' => trim($etichetteutente[$chiave]), 'primamaiuscola' => true));
                        } else {
                            $nomicolonne[$indicecolonna] = isset($singoloalias['descrizione']) ? $singoloalias['descrizione'] : self::to_camel_case(array('str' => $chiave, 'primamaiuscola' => true));
                        }

                        if ((isset($larghezzeutente[$chiave])) && ($larghezzeutente[$chiave] != '') && ($larghezzeutente[$chiave] != 0)) {
                            $widthcampo = $larghezzeutente[$chiave];
                        } else {
                            $widthcampo = isset($singoloalias['lunghezza']) ? $singoloalias['lunghezza'] : ($colonna['length'] * $moltiplicatorelarghezza > $larghezzamassima ? $larghezzamassima : $colonna['length'] * $moltiplicatorelarghezza);
                        }

                        if (isset($singoloalias['tipo']) && ($singoloalias['tipo'] == 'select')) {
                            $modellocolonne[$indicecolonna] = array('name' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave, 'id' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave, 'width' => $widthcampo, 'tipocampo' => isset($singoloalias['tipo']) ? $singoloalias['tipo'] : $colonna['type'], 'editable' => isset($singoloalias['editable']) ? $singoloalias['editable'] : null, 'editoptions' => $singoloalias['valoricombo']);
                        } else {
                            $modellocolonne[$indicecolonna] = array('name' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave, 'id' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave, 'width' => $widthcampo, 'tipocampo' => isset($singoloalias['tipo']) ? $singoloalias['tipo'] : $colonna['type'], 'editable' => isset($singoloalias['editable']) ? $singoloalias['editable'] : null);
                        }
                    }
                } else {
                    if (isset($ordinecolonne)) {
                        $indicecolonna = array_search($chiave, $ordinecolonne);
                        if ($indicecolonna === false) {
                            if ($indice === 0) {
                                $indice = count($ordinecolonne);
                            }
                            ++$indice;
                            $indicecolonna = $indice;
                        } else {
                            if ($indicecolonna > $indice) {
                                $indice = $indicecolonna;
                            }
                        }
                    } else {
                        ++$indice;
                        $indicecolonna = $indice;
                    }
                    if ((isset($etichetteutente[$chiave])) && (trim($etichetteutente[$chiave]) != '')) {
                        $nomicolonne[$indicecolonna] = self::to_camel_case(array('str' => trim($etichetteutente[$chiave]), 'primamaiuscola' => true));
                    } else {
                        $nomicolonne[$indicecolonna] = self::to_camel_case(array('str' => $chiave, 'primamaiuscola' => true));
                    }

                    if ((isset($larghezzeutente[$chiave])) && ($larghezzeutente[$chiave] != '') && ($larghezzeutente[$chiave] != 0)) {
                        $widthcampo = $larghezzeutente[$chiave];
                    } else {
                        $widthcampo = ($colonna['length'] * $moltiplicatorelarghezza > $larghezzamassima ? $larghezzamassima : $colonna['length'] * $moltiplicatorelarghezza);
                    }

                    $modellocolonne[$indicecolonna] = array('name' => $chiave, 'id' => $chiave, 'width' => $widthcampo, 'tipocampo' => $colonna['type']);
                }
            }
        }

        if ($output != 'stampa') {
            // Controlla se alcune colonne devono essere dei link
            if (isset($colonne_link)) {
                foreach ($colonne_link as $colonna_link) {
                    foreach ($colonna_link as $nomecolonna => $parametricolonna) {
                        foreach ($modellocolonne as $key => $value) {
                            foreach ($value as $keyv => $valuev) {
                                if (($keyv == 'name') && ($valuev == $nomecolonna)) {
                                    $modellocolonne[$key]['formatter'] = 'showlink';
                                    $modellocolonne[$key]['formatoptions'] = $parametricolonna;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Controlla se ci sono dei campi extra da inserire in griglia (i campi extra non sono utilizzabili come filtri nella filtertoolbar della griglia)
        if (isset($campiextra)) {
            foreach ($campiextra as $chiave => $colonna) {
                ++$indice;
                if (is_object($colonna)) {
                    $colonna = get_object_vars($colonna);
                }
                $nomicolonne[$indice] = isset($colonna['descrizione']) ? $colonna['descrizione'] : self::to_camel_case(array('str' => $chiave, 'primamaiuscola' => true));
                $modellocolonne[$indice] = array('name' => isset($colonna['nomecampo']) ? $colonna['nomecampo'] : $chiave, 'id' => isset($colonna['nomecampo']) ? $colonna['nomecampo'] : $chiave, 'width' => isset($colonna['lunghezza']) ? $colonna['lunghezza'] : ($colonna['length'] * $moltiplicatorelarghezza > $larghezzamassima ? $larghezzamassima : $colonna['length'] * $moltiplicatorelarghezza), 'tipocampo' => isset($colonna['tipo']) ? $colonna['tipo'] : $colonna['type'], 'search' => false);
            }
        }
        ksort($nomicolonne);
        ksort($modellocolonne);
        $nomicolonnesorted = array();
        foreach ($nomicolonne as $key => $value) {
            $nomicolonnesorted[] = $value;
        }
        $modellocolonnesorted = array();
        foreach ($modellocolonne as $key => $value) {
            $modellocolonnesorted[] = $value;
        }
        $testata['tabella'] = $nometabella;
        $testata['nomicolonne'] = $nomicolonnesorted;
        $testata['modellocolonne'] = $modellocolonnesorted;
        $testata['output'] = $output;

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

        if (isset($paricevuti['container'])) {
            $permessi = new GestionepermessiController();
            $permessi->setContainer($paricevuti['container']);

            $vettorepermessi = $permessi->impostaPermessi(array('modulo' => $paricevuti['nometabella']));
            $testata = array_merge($testata, $vettorepermessi);
        }

        return $testata;
    }

    /**
     * Questa funzione è compatibile con jqGrid e risponde con un formato JSON contenente
     * i dati di risposta sulla base dei parametri passati.
     *
     * @param array  $paricevuti
     * @param object $paricevuti[request]        oggetto che contiene il POST passato alla griglia
     * @param string $paricevuti[nometabella]
     * @param array  $paricevuti[tabellej]       array contenente tutte le tabelle per le quali richiedere
     *                                           la join a partire da $paricevuti[nometabella]
     * @param array  $paricevuti[escludere]      array contenente tutti i campi che non devono essere restituiti
     * @param bool   $paricevuti[nospan]         se true non imposta limit e offset
     * @param array  $paricevuti[parametri_link] array contenente le colonne che devono essere rappresentate
     *                                           come dei link e relativi parametri per comporre l'href.
     *                                           Da non confondere con colonne_link che si passa a
     *                                           testataPerGriglia, perchè QUESTO array genera un
     *                                           tag <href> interno alla colonna per il quale si
     *                                           possono specificare le parti che lo compongono
     * @param array  $paricevuti[decodifiche]    = array contenente eventuali decodifiche dei valori di
     *                                           una colonna che non può essere tradotta con una join
     *                                           ad una tabella
     * @param string $paricevuti[output]         : "index" se i dati servono per la griglia dell'index, "stampa" se i dati servono per la stampa
     *
     * @return JSON con i dati richiesti
     */
    public static function datiPerGriglia($paricevuti = array()) {
        $request = $paricevuti['request'];

        if ((isset($paricevuti['output'])) && ($paricevuti['output'] == 'stampa')) {
            $output = 'stampa';
        } else {
            $output = 'index';
        }

        $doctrine = self::getDoctrineByEm($paricevuti);
        $doctrineficore = self::getDoctrineFiCoreByEm($paricevuti, $doctrine);

        $bundle = $paricevuti['nomebundle'];
        $nometabella = $paricevuti['nometabella'];
        $tabellej = (isset($paricevuti['tabellej']) ? $paricevuti['tabellej'] : null);
        if (is_object($tabellej)) {
            $tabellej = get_object_vars($tabellej);
        }

        $decodifiche = (isset($paricevuti['decodifiche']) ? $paricevuti['decodifiche'] : null);
        $escludere = (isset($paricevuti['escludere']) ? $paricevuti['escludere'] : null);
        $escludereutente = self::campiesclusi($paricevuti);
        $nospan = (isset($paricevuti['nospan']) ? $paricevuti['nospan'] : false);
        /* $precondizioniGET = $request->get('precondizioni');
          if (isset($precondizioniGET)) {
          $precondizioni = $precondizioniGET;
          } else {
          $precondizioni = ((isset($paricevuti["precondizioni"]) && (count($paricevuti["precondizioni"]) > 0)) ? $paricevuti["precondizioni"] : false);
          } */
        $precondizioni = (isset($paricevuti['precondizioni']) ? $paricevuti['precondizioni'] : false);

        $precondizioniAvanzate = (isset($paricevuti['precondizioniAvanzate']) ? $paricevuti['precondizioniAvanzate'] : false);
        /* $parametri_link = (isset($paricevuti['parametri_link']) ? $paricevuti['parametri_link'] : null); //$paricevuti["parametri_link"]; */
        $campiextra = (isset($paricevuti['campiextra']) ? $paricevuti['campiextra'] : null);
        $ordinecolonne = (isset($paricevuti['ordinecolonne']) ? $paricevuti['ordinecolonne'] : null);
        if (!isset($ordinecolonne)) {
            $ordinecolonne = self::ordinecolonne($paricevuti);
        }
        // inserisco i filtri passati in un vettore

        $filtri = json_decode($request->get('filters'), true);
        // inserisco i parametri che sono passati nella $request all'interno di
        // apposite variabili
        // che pagina siamo
        $page = $request->get('page'); // get the requested page
        // quante righe restituire (in caso di nospan = false)
        $limit = $request->get('rows'); // get how many rows we want to have into the grid
        // su quale campo fare l'ordinamento
        $sidx = $request->get('sidx'); // get index row - i.e. user click to sort
        // direzione dell'ordinamento
        $sord = $request->get('sord'); // get the direction if(!$sidx) $sidx =1;
        // se non è passato nessun campo (ipotesi peregrina) usa id
        if (!$sidx) {
            $sidx = $nometabella . '.id';
        } elseif (strrpos($sidx, '.') == 0) {
            if (strrpos($sidx, ',') == 0) {
                $sidx = $nometabella . '.' . $sidx; // un solo campo
            } else { // più campi, passati separati da virgole
                $parti = explode(',', $sidx);
                $sidx = '';
                foreach ($parti as $parte) {
                    if (trim($sidx) != '') {
                        $sidx = $sidx . ',';
                    }
                    $sidx = $sidx . $nometabella . '.' . trim($parte);
                }
            }
        }
        // inizia la query
        $entityName = $bundle . ':' . $nometabella;
        $q = $doctrine->createQueryBuilder();
        $q->select($nometabella)
                ->from($entityName, $nometabella);

        // scorre le tabelle collegate e crea la leftjoin usando come alias il nome stesso della tabella
        if (isset($tabellej)) {
            self::setTabelleJoin($q, array('tabellej' => $tabellej, 'nometabella' => $nometabella));
        }

        // dal filtro prende il tipo di operatore (AND o OR sono i due fin qui gestiti)
        $tipof = $filtri['groupOp'];
        // prende un vettore con tutte le ricerche
        $regole = $filtri['rules'];

        self::init();

        //se ci sono delle precondizioni le imposta qui
        $primo = true;
        if ($precondizioni) {
            self::setPrecondizioni($q, $primo, array('precondizioni' => $precondizioni));
        }

        //se ci sono delle precondizioni avanzate le imposta qui
        if ($precondizioniAvanzate) {
            self::setPrecondizioniAvanzate(
                    $q, $primo, array('precondizioniAvanzate' => $precondizioniAvanzate,
                'doctrine' => $doctrine,
                'nometabella' => $nometabella,
                'entityName' => $entityName,
                'bundle' => $bundle,)
            );
        }

        // scorro ogni singola regola
        if (isset($regole)) {
            self::setRegole(
                    $q, $primo, array(
                'regole' => $regole,
                'doctrine' => $doctrine,
                'nometabella' => $nometabella,
                'entityName' => $entityName,
                'bundle' => $bundle,
                'tipof' => $tipof,
                    )
            );
        }
        // conta il numero di record di risposta
        // $query_tutti_records = $q->getQuery();
        // $quanti = count($query_tutti_records->getSingleScalarResult());

        $paginator = new Paginator($q, true);
        $quanti = count($paginator);

        // imposta l'offset, ovvero il record dal quale iniziare a visualizzare i dati
        $offset = ($limit * ($page - 1));

        // se si mandano i dati in stampa non tiene conto di limite e offset ovvero risponde con tutti i dati
        if ($output != 'stampa') {
            // se nospan non tiene conto di limite e offset ovvero risponde con tutti i dati
            if (!($nospan)) {
                //Imposta il limite ai record da estrarre
                $q = ($limit ? $q->setMaxResults($limit) : $q);
                //E imposta il primo record da visualizzare (per la paginazione)
                $q = ($offset ? $q->setFirstResult($offset) : $q);
            }
        } else {
            if ($quanti > 1000) {
                set_time_limit(960);
                ini_set('memory_limit', '2048M');
            }
        }

        if ($sidx) {
            $q->orderBy($sidx, $sord);
        }
        //Dall'oggetto querybuilder si ottiene la query da eseguire
        $query_paginata = $q->getQuery();

        ///*Object*/
        //$q = $query_paginata->getResult();
        ///*array*/
        //Si ottiene un array con tutti i records
        $q = $query_paginata->getArrayResult();
        //Se il limire non è stato impostato si mette 1 (per calcolare la paginazione)
        $limit = ($limit ? $limit : 1);
        // calcola in mumero di pagine totali necessarie

        $total_pages = ceil($quanti / ($limit == 0 ? 1 : $limit));

        // imposta in $vettorerisposta la risposta strutturata per essere compresa da jqgrid
        $vettorerisposta = array();
        $vettorerisposta['page'] = $page;
        $vettorerisposta['total'] = $total_pages;
        $vettorerisposta['records'] = $quanti;
        $vettorerisposta['filtri'] = $filtri;
        $indice = 0;

        //Si scorrono tutti i records della query
        foreach ($q as $singolo) {
            //Si scorrono tutti i campi del record
            $vettoreriga = array();
            foreach ($singolo as $nomecampo => $singolocampo) {
                //Si controlla se il campo è da escludere o meno
                if ((!isset($escludere) || !(in_array($nomecampo, $escludere))) && (!isset($escludereutente) || !(in_array($nomecampo, $escludereutente)))) {
                    if (isset($tabellej[$nomecampo])) {
                        if (is_object($tabellej[$nomecampo])) {
                            $tabellej[$nomecampo] = get_object_vars($tabellej[$nomecampo]);
                        }
                        //Per ogni campo si cattura il valore dall'array che torna doctrine
                        foreach ($tabellej[$nomecampo]['campi'] as $campoelencato) {
                            ///*Object*/
                            //$fields = $singolo->get($tabellej[$nomecampo]["tabella"]) ? $singolo->get($tabellej[$nomecampo]["tabella"])->get($campoelencato) : "";
                            ///*array*/

                            if (isset($ordinecolonne)) {
                                $indicecolonna = array_search($nomecampo, $ordinecolonne);
                                if ($indicecolonna === false) {
                                    if ($indice === 0) {
                                        $indice = count($ordinecolonne);
                                    }
                                    ++$indice;
                                    $indicecolonna = $indice;
                                } else {
                                    if ($indicecolonna > $indice) {
                                        $indice = $indicecolonna;
                                    }
                                }
                            } else {
                                ++$indice;
                                $indicecolonna = $indice;
                            }

                            $parametriCampoElencato['tabellej'] = $tabellej;
                            $parametriCampoElencato['nomecampo'] = $nomecampo;
                            $parametriCampoElencato['campoelencato'] = $campoelencato;
                            $parametriCampoElencato['vettoreriga'] = $vettoreriga;
                            $parametriCampoElencato['singolo'] = $singolo;
                            $parametriCampoElencato['doctrine'] = $doctrine;
                            $parametriCampoElencato['bundle'] = $bundle;
                            $parametriCampoElencato['ordinecampo'] = $indicecolonna;
                            $parametriCampoElencato['decodifiche'] = $decodifiche;

                            $vettoreriga = self::campoElencato($parametriCampoElencato);
                        }
                    } else {
                        if (isset($ordinecolonne)) {
                            $indicecolonna = array_search($nomecampo, $ordinecolonne);
                            if ($indicecolonna === false) {
                                if ($indice === 0) {
                                    $indice = count($ordinecolonne);
                                }
                                ++$indice;
                                $indicecolonna = $indice;
                            } else {
                                if ($indicecolonna > $indice) {
                                    $indice = $indicecolonna;
                                }
                            }
                        } else {
                            ++$indice;
                            $indicecolonna = $indice;
                        }

                        self::valorizzaVettore($vettoreriga, array('singolocampo' => $singolocampo, 'tabella' => $bundle . ':' . $nometabella, 'nomecampo' => $nomecampo, 'doctrine' => $doctrine, 'ordinecampo' => $indicecolonna, 'decodifiche' => $decodifiche));
                    }
                }
            }

            //Gestione per passare campi che non sono nella tabella ma metodi del model (o richiamabili tramite magic method get)
            if (isset($campiextra)) {
                if (count($campiextra) > 1) {
                    foreach ($campiextra as $vettore) {
                        foreach ($vettore as $nomecampo => $singolocampo) {
                            $campo = 'get' . ucfirst($singolocampo);
                            /* @var $doctrine \Doctrine\ORM\EntityManager */
                            $objTabella = $doctrine->find($entityName, $singolo['id']);
                            $vettoreriga[] = $objTabella->$campo();
                        }
                    }
                } else {
                    foreach ($campiextra as $nomecampo => $singolocampo) {
                        $campo = 'get' . ucfirst($singolocampo);
                        /* @var $doctrine \Doctrine\ORM\EntityManager */
                        $objTabella = $doctrine->find($entityName, $singolo['id']);
                        $vettoreriga[] = $objTabella->$campo();
                    }
                }
            }

            //Si costruisce la risposta json per la jqgrid
            ksort($vettoreriga);
            $vettorerigasorted = array();
            foreach ($vettoreriga as $value) {
                $vettorerigasorted[] = $value;
            }
            $vettorerisposta['rows'][] = array('id' => $singolo['id'], 'cell' => $vettorerigasorted);
            unset($vettoreriga);
        }

        return json_encode($vettorerisposta);
    }

    public static function valorizzaVettore(&$vettoreriga, $parametri) {
        $tabella = $parametri['tabella'];
        $nomecampo = $parametri['nomecampo'];
        $doctrine = $parametri['doctrine'];
        $ordinecampo = $parametri['ordinecampo'];
        $decodifiche = $parametri['decodifiche'];

        $vettoreparcampi = $doctrine->getMetadataFactory()->getMetadataFor($tabella)->fieldMappings;

        if (is_object($vettoreparcampi)) {
            $vettoreparcampi = get_object_vars($vettoreparcampi);
        }

        $singolocampo = $parametri['singolocampo'];

        if (isset($decodifiche[$nomecampo])) {
            $vettoreriga[] = $decodifiche[$nomecampo][$singolocampo];
        } else {
            if (isset($vettoreparcampi[$nomecampo]['type']) && ($vettoreparcampi[$nomecampo]['type'] == 'date' || $vettoreparcampi[$nomecampo]['type'] == 'datetime') && $singolocampo) {
                if (isset($ordinecampo)) {
                    $vettoreriga[$ordinecampo] = $singolocampo->format('d/m/Y');
                } else {
                    $vettoreriga[] = $singolocampo->format('d/m/Y');
                }
            } elseif (isset($vettoreparcampi[$nomecampo]['type']) && ($vettoreparcampi[$nomecampo]['type'] == 'time') && $singolocampo) {
                if (isset($ordinecampo)) {
                    $vettoreriga[$ordinecampo] = $singolocampo->format('H:i');
                } else {
                    $vettoreriga[] = $singolocampo->format('H:i');
                }
            } else {
                if (isset($ordinecampo)) {
                    $vettoreriga[$ordinecampo] = $singolocampo;
                } else {
                    $vettoreriga[] = $singolocampo;
                }
            }
        }
    }

    public static function campoElencato($parametriCampoElencato) {
        $tabellej = $parametriCampoElencato['tabellej'];
        $nomecampo = $parametriCampoElencato['nomecampo'];
        $campoelencato = $parametriCampoElencato['campoelencato'];
        $vettoreriga = $parametriCampoElencato['vettoreriga'];
        $singolo = $parametriCampoElencato['singolo'];
        $doctrine = $parametriCampoElencato['doctrine'];
        $bundle = $parametriCampoElencato['bundle'];
        $decodifiche = $parametriCampoElencato['decodifiche'];

        if (isset($parametriCampoElencato['ordinecampo'])) {
            $ordinecampo = $parametriCampoElencato['ordinecampo'];
        } else {
            $ordinecampo = null;
        }

        if (isset($tabellej[$campoelencato])) {
            foreach ($tabellej[$campoelencato]['campi'] as $campoelencatointerno) {
                $parametriCampoElencatoInterno['tabellej'] = $tabellej;
                $parametriCampoElencatoInterno['nomecampo'] = $campoelencato;
                $parametriCampoElencatoInterno['campoelencato'] = $campoelencatointerno;
                $parametriCampoElencatoInterno['vettoreriga'] = $vettoreriga;
                $parametriCampoElencatoInterno['singolo'] = $singolo;
                $parametriCampoElencatoInterno['doctrine'] = $doctrine;
                $parametriCampoElencatoInterno['bundle'] = $bundle;
                $parametriCampoElencatoInterno['ordinecampo'] = $ordinecampo;
                $parametriCampoElencatoInterno['decodifiche'] = $decodifiche;

                $vettoreriga = self::campoElencato($parametriCampoElencatoInterno);
            }
        } else {
            if (isset($tabellej[$nomecampo]['padre'])) {
                $fields = $singolo[$tabellej[$nomecampo]['padre']][$tabellej[$nomecampo]['tabella']] ? $singolo[$tabellej[$nomecampo]['padre']][$tabellej[$nomecampo]['tabella']][$campoelencato] : '';
            } else {
                $fields = $singolo[$tabellej[$nomecampo]['tabella']] ? $singolo[$tabellej[$nomecampo]['tabella']][$campoelencato] : '';
            }
            self::valorizzaVettore($vettoreriga, array('singolocampo' => $fields, 'tabella' => $bundle . ':' . $tabellej[$nomecampo]['tabella'], 'nomecampo' => $campoelencato, 'doctrine' => $doctrine, 'ordinecampo' => $ordinecampo, 'decodifiche' => $decodifiche));
        }

        return $vettoreriga;
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

    /**
     * Funzione alla quale si passano i filtri nel formato gestito da jqGrid e
     * che restituisce una stringa che contiene la descrizione in linguaggio
     * naturale.
     *
     * @param array   $parametri
     * @param stringa $filtri
     *
     * @return string
     */
    public static function traduciFiltri($parametri = array()) {
        $genericofiltri = $parametri['filtri'];

        $filtri = isset($genericofiltri->rules) ? $genericofiltri->rules : '';
        $tipofiltro = isset($genericofiltri->groupOp) ? $genericofiltri->groupOp : '';
        self::$decodificaop = array('eq' => ' è uguale a ', 'ne' => ' è diverso da ', 'lt' => ' è inferiore a ', 'le' => ' è inferiore o uguale a ', 'gt' => ' è maggiore di ', 'ge' => ' è maggiore o uguale di ', 'bw' => ' comincia con ', 'bn' => ' non comincia con ', 'in' => ' è uno fra ', 'ni' => ' non è uno fra ', 'ew' => ' finisce con ', 'en' => ' con finisce con ', 'cn' => ' contiene ', 'nc' => ' non contiene ', 'nu' => ' è vuoto', 'nn' => ' non è vuoto');

        if (!isset($filtri) or ( !$filtri)) {
            return '';
        }

        $filtrodescritto = ('I dati mostrati rispondono a' . ($tipofiltro == 'AND' ? ' tutti i' : 'd almeno uno dei') . ' seguenti criteri: ');

        foreach ($filtri as $indice => $filtro) {
            $campo = $filtro->field;
            $operatore = $filtro->op;
            $data = $filtro->data;
            $filtrodescritto .= ($indice !== 0 ? ($tipofiltro == 'AND' ? ' e ' : ' o ') : '') . self::to_camel_case(array('str' => $campo, 'primamaiuscola' => true)) . self::$decodificaop[$operatore] . "\"$data\"";
        }

        $filtrodescritto .= '.';

        return $filtrodescritto;
    }

}

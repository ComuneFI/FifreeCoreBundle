<?php

namespace Fi\CoreBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Fi\CoreBundle\DependencyInjection\GrigliaUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaRegoleUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaCampiExtraUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaColonneUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaDatiUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaDatiPrecondizioniUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaExtraFunzioniUtils;

class Griglia extends FiController {

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
        $output = GrigliaUtils::getOuputType($paricevuti);

        $doctrine = GrigliaUtils::getDoctrineByEm($paricevuti);
        $doctrineficore = GrigliaUtils::getDoctrineFiCoreByEm($paricevuti, $doctrine);

        $testata = array();
        $nomicolonne = array();
        $modellocolonne = array();
        $indice = 0;

        GrigliaColonneUtils::getColonne($nomicolonne, $modellocolonne, $indice, $paricevuti);

        /* Controlla se alcune colonne devono essere dei link */
        GrigliaExtraFunzioniUtils::getColonneLink($paricevuti, $modellocolonne);

        /* Controlla se ci sono dei campi extra da inserire in griglia (i campi extra non sono utilizzabili come filtri nella filtertoolbar della griglia) */
        GrigliaCampiExtraUtils::getCampiExtraTestataPerGriglia($paricevuti, $indice, $nomicolonne, $modellocolonne);

        GrigliaUtils::getOpzioniTabella($doctrineficore, $nometabella, $testata);

        GrigliaUtils::getPermessiTabella($paricevuti, $testata);

        $testata['nomicolonne'] = GrigliaUtils::getNomiColonne($nomicolonne);
        $testata['modellocolonne'] = GrigliaUtils::getModelloColonne($modellocolonne);

        $testata['tabella'] = $nometabella;
        $testata['output'] = $output;

        return $testata;
    }

    /**
     * Questa funzione è compatibile con jqGrid e risponde con un formato JSON contenente
     * i dati di risposta sulla base dei parametri passati.
     *
     * @param array  $parametri
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
    public static function datiPerGriglia($parametri = array()) {
        $request = $parametri['request'];

        $output = GrigliaUtils::getOuputType($parametri);

        $doctrine = GrigliaUtils::getDoctrineByEm($parametri);
        /* $doctrineficore = GrigliaUtils::getDoctrineFiCoreByEm($paricevuti, $doctrine); */

        $bundle = $parametri['nomebundle'];
        $nometabella = $parametri['nometabella'];
        /* qui */
        $tabellej = GrigliaDatiUtils::getTabellejNormalizzate($parametri);

        $decodifiche = GrigliaDatiUtils::getDatiDecodifiche($parametri);
        $escludere = GrigliaDatiUtils::getDatiEscludere($parametri);
        $escludereutente = GrigliaDatiUtils::getDatiEscludere($parametri);
        $nospan = GrigliaDatiUtils::getDatiNospan($parametri);

        $precondizioni = GrigliaDatiUtils::getDatiPrecondizioni($parametri);

        $precondizioniAvanzate = GrigliaDatiUtils::getDatiPrecondizioniAvanzate($parametri);
        /* $parametri_link = (isset($paricevuti['parametri_link']) ? $paricevuti['parametri_link'] : null); //$paricevuti["parametri_link"]; */
        $campiextra = GrigliaDatiUtils::getDatiCampiExtra($parametri);
        $ordinecolonne = GrigliaDatiUtils::getDatiOrdineColonne($parametri);
        /* inserisco i filtri passati in un vettore */

        $filtri = json_decode($request->get('filters'), true);
        /* inserisco i parametri che sono passati nella $request all'interno di
          apposite variabili in che pagina siamo */
        $page = $request->get('page'); // get the requested page
        /* quante righe restituire (in caso di nospan = false) */
        $limit = $request->get('rows'); // get how many rows we want to have into the grid
        /* su quale campo fare l'ordinamento */
        $sidx = $request->get('sidx'); // get index row - i.e. user click to sort
        /* direzione dell'ordinamento */
        $sord = $request->get('sord'); // get the direction if(!$sidx) $sidx =1;
        GrigliaDatiUtils::getDatiOrdinamento($sidx, $nometabella);
        /* inizia la query */
        $entityName = $bundle . ':' . $nometabella;
        $q = $doctrine->createQueryBuilder();
        $q->select($nometabella)
                ->from($entityName, $nometabella);

        /* scorre le tabelle collegate e crea la leftjoin usando come alias il nome stesso della tabella */
        if (isset($tabellej)) {
            GrigliaDatiUtils::setTabelleJoin($q, array('tabellej' => $tabellej, 'nometabella' => $nometabella));
        }

        /* dal filtro prende il tipo di operatore (AND o OR sono i due fin qui gestiti) */
        $tipof = $filtri['groupOp'];
        /* prende un vettore con tutte le ricerche */
        $regole = $filtri['rules'];

        GrigliaUtils::init();

        /* se ci sono delle precondizioni le imposta qui */
        $primo = true;
        if ($precondizioni) {
            var_dump($precondizioni);exit;
            GrigliaDatiPrecondizioniUtils::setPrecondizioni($q, $primo, array('precondizioni' => $precondizioni));
        }

        /* se ci sono delle precondizioni avanzate le imposta qui */
        if ($precondizioniAvanzate) {
            GrigliaDatiPrecondizioniUtils::setPrecondizioniAvanzate(
                    $q, $primo, array('precondizioniAvanzate' => $precondizioniAvanzate,
                'doctrine' => $doctrine,
                'nometabella' => $nometabella,
                'entityName' => $entityName,
                'bundle' => $bundle,)
            );
        }
        /* scorro ogni singola regola */
        if (isset($regole)) {
            GrigliaRegoleUtils::setRegole(
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

                            $vettoreriga = GrigliaDatiUtils::campoElencato($parametriCampoElencato);
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

                        GrigliaDatiUtils::valorizzaVettore($vettoreriga, array('singolocampo' => $singolocampo, 'tabella' => $bundle . ':' . $nometabella, 'nomecampo' => $nomecampo, 'doctrine' => $doctrine, 'ordinecampo' => $indicecolonna, 'decodifiche' => $decodifiche));
                    }
                }
            }

//Gestione per passare campi che non sono nella tabella ma metodi del model (o richiamabili tramite magic method get)
            if (isset($campiextra)) {
                if (count($campiextra) == count($campiextra, \COUNT_RECURSIVE)) {
                    $campiextra[0] = $campiextra;
                }
                foreach ($campiextra as $vettore) {
                    foreach ($vettore as $nomecampo => $singolocampo) {
                        $campo = 'get' . ucfirst($singolocampo);
                        /* @var $doctrine \Doctrine\ORM\EntityManager */
                        $objTabella = $doctrine->find($entityName, $singolo['id']);
                        $vettoreriga[] = $objTabella->$campo();
                    }
                }
            }

            /* Si costruisce la risposta json per la jqgrid */
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

}

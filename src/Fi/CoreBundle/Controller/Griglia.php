<?php

namespace Fi\CoreBundle\Controller;

use Fi\CoreBundle\DependencyInjection\GrigliaUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaRegoleUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaCampiExtraUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaColonneUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaDatiUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaDatiPrecondizioniUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaExtraFunzioniUtils;
use Fi\CoreBundle\DependencyInjection\GrigliaDatiMultiUtils;

class Griglia extends FiController
{
    /**
     * Questa funzione è compatibile con jqGrid e risponden con un formato JSON
     * contenente i dati di testata per la griglia.
     *
     * @param array  $paricevuti
     * @param object $paricevuti[request]      oggetto che contiene il POST passato alla griglia
     * @param string $paricevuti[nometabella]
     * @param array  $paricevuti[dettaglij]    array contenente tutte le tabelle per le quali richiedere
     *                                         la join a partire da $paricevuti[nometabella]
     *                                         il vettore è composto da
     *                                         array("nomecampodadecodificare"=>array(
     *                                         "descrizione"=>"nometabella.campodecodifica",
     *                                         "lunghezza"=>"40")
     *                                         )
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
    public static function testataPerGriglia($paricevuti = array())
    {
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

        /* Controlla se ci sono dei campi extra da inserire in griglia
          (i campi extra non sono utilizzabili come filtri nella filtertoolbar della griglia) */
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
    public static function datiPerGriglia($parametri = array())
    {
        $request = $parametri['request'];
        $doctrine = GrigliaUtils::getDoctrineByEm($parametri);
        /* $doctrineficore = GrigliaUtils::getDoctrineFiCoreByEm($paricevuti, $doctrine); */
        $bundle = $parametri['nomebundle'];
        $nometabella = $parametri['nometabella'];
        /* qui */
        $tabellej = GrigliaDatiUtils::getTabellejNormalizzate($parametri);

        $precondizioni = GrigliaDatiUtils::getDatiPrecondizioni($parametri);

        $precondizioniAvanzate = GrigliaDatiUtils::getDatiPrecondizioniAvanzate($parametri);
        /* $parametri_link = (isset($paricevuti['parametri_link']) ? $paricevuti['parametri_link'] : null); //$paricevuti["parametri_link"]; */
        $campiextra = GrigliaDatiUtils::getDatiCampiExtra($parametri);
        /* inserisco i filtri passati in un vettore */

        $filtri = json_decode($request->get('filters'), true);
        /* inserisco i parametri che sono passati nella $request all'interno di
          apposite variabili in che pagina siamo */
        /* direzione dell'ordinamento */
        $sord = $request->get('sord'); // get the direction if(!$sidx) $sidx =1;
        $page = $request->get('page'); // get the requested page
        /* quante righe restituire (in caso di nospan = false) */
        $limit = $request->get('rows'); // get how many rows we want to have into the grid
        /* su quale campo fare l'ordinamento */
        $sidx = $request->get('sidx'); // get index row - i.e. user click to sort
        /* direzione dell'ordinamento */
        $sord = $request->get('sord'); // get the direction if(!$sidx) $sidx =1;
        GrigliaDatiUtils::getDatiOrdinamento($sidx, $nometabella);
        /* inizia la query */
        $entityName = $bundle.':'.$nometabella;
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
            GrigliaDatiPrecondizioniUtils::setPrecondizioni($q, $primo, array('precondizioni' => $precondizioni));
        }

        /* se ci sono delle precondizioni avanzate le imposta qui */
        if ($precondizioniAvanzate) {
            GrigliaDatiPrecondizioniUtils::setPrecondizioniAvanzate(
                $q,
                $primo,
                array('precondizioniAvanzate' => $precondizioniAvanzate,
                'doctrine' => $doctrine,
                'nometabella' => $nometabella,
                'entityName' => $entityName,
                'bundle' => $bundle, )
            );
        }
        /* scorro ogni singola regola */
        if (isset($regole)) {
            GrigliaRegoleUtils::setRegole(
                $q,
                $primo,
                array(
                'regole' => $regole,
                'doctrine' => $doctrine,
                'nometabella' => $nometabella,
                'entityName' => $entityName,
                'bundle' => $bundle,
                'tipof' => $tipof,
                    )
            );
        }
        $quanti = 0;
        GrigliaDatiMultiUtils::prepareQuery($parametri, $q, $sidx, $sord, $page, $limit, $quanti);

        $total_pages = GrigliaDatiMultiUtils::getTotalPages($quanti, $limit);

        /* imposta in $vettorerisposta la risposta strutturata per essere compresa da jqgrid */
        $vettorerisposta = array();
        $vettorerisposta['page'] = $page;
        $vettorerisposta['total'] = $total_pages;
        $vettorerisposta['records'] = $quanti;
        $vettorerisposta['filtri'] = $filtri;
        $indice = 0;

        /* Si scorrono tutti i records della query */
        foreach ($q as $singolo) {
            /* Si scorrono tutti i campi del record */
            $vettoreriga = array();
            $indicecolonna = 0;
            foreach ($singolo as $nomecampo => $singolocampo) {
                GrigliaDatiMultiUtils::buildColonneDatiGriglia(
                    $parametri,
                    $vettoreriga,
                    $singolo,
                    $nomecampo,
                    $nomecampo,
                    $indice,
                    $indicecolonna,
                    $singolocampo
                );
            }

            GrigliaCampiExtraUtils::getCampiExtraDatiPerGriglia($campiextra, $vettoreriga, $doctrine, $entityName, $singolo);

            GrigliaDatiMultiUtils::buildRowGriglia($singolo, $vettoreriga, $vettorerisposta);
        }

        return json_encode($vettorerisposta);
    }
}

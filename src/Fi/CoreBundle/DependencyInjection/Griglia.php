<?php

namespace Fi\CoreBundle\DependencyInjection;

use Fi\CoreBundle\Utils\GridDati;
use Fi\CoreBundle\Utils\GridTestata;
use Fi\CoreBundle\Utils\GrigliaParametriUtils;
use Fi\CoreBundle\Utils\GrigliaUtils;
use Fi\CoreBundle\Utils\GrigliaCampiExtraUtils;
use Fi\CoreBundle\Utils\GrigliaColonneUtils;
use Fi\CoreBundle\Utils\GrigliaDatiUtils;
use Fi\CoreBundle\Utils\GrigliaDatiPrecondizioniUtils;
use Fi\CoreBundle\Utils\GrigliaExtraFunzioniUtils;
use Fi\CoreBundle\Utils\GrigliaDatiMultiUtils;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Griglia
{

    /**
     * Questa funzione è compatibile con jqGrid e risponde con un formato JSON
     * contenente i dati di testata per la griglia.
     *
     * @param array  $paricevuti
     * <pre>object $paricevuti[request]      oggetto che contiene il POST passato alla griglia</pre>
     * <pre>string $paricevuti[nometabella]  Nome della tabella</pre>
     * <pre>array  $paricevuti[dettaglij]    array contenente tutte le tabelle per le quali richiedere
     *                                         la join a partire da $paricevuti[nometabella]
     *                                         il vettore è composto da
     *                                         array("nomecampodadecodificare"=>array(
     *                                         "descrizione"=>"nometabella.campodecodifica",
     *                                         "lunghezza"=>"40")
     *                                         )</pre>
     * <pre>array  $paricevuti[colonne_link] array contenente eventuali colonne che debbano essere
     *                                         rappresentate da un link. Non è da confondere con i
     *                                         parametri_link di datiPerGriglia, perché QUESTO array
     *                                         si può passare alla testata se si vuole avere una
     *                                         colonna link che prenda in automatico
     *                                         parametro id = al valore dell'id della tabella
     *                                         principale su cui si sta facendo la griglia</pre>
     * <pre>string $paricevuti[output]       : "index" se la testata serve per la griglia dell'index, "stampa" se la testata serve per la stampa</pre>
     *
     * @return array contentente i dati di testata per la griglia
     */
    public function testataPerGriglia($paricevuti = [])
    {
        $nometabella = $paricevuti['nometabella'];
        $nomebundle = $paricevuti['nomebundle'];
        $doctrine = GrigliaParametriUtils::getDoctrineByEm($paricevuti);
        $doctrineficore = GrigliaParametriUtils::getDoctrineFiCoreByEm($paricevuti, $doctrine);

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
        $testata['nomebundle'] = $nomebundle;
        $testata['output'] = GrigliaParametriUtils::getOuputType($paricevuti);

        $response = new GridTestata($testata);
        return $response->getResponse();
    }

    /**
     * Questa funzione è compatibile con jqGrid e risponde con un formato JSON contenente
     * i dati di risposta sulla base dei parametri passati.
     *
     * @param array  $parametri
     * <pre>object $parametri["request"]        oggetto che contiene il POST passato alla griglia</pre>
     * <pre>string $parametri["nometabella"]   Nome della tabella</pre>
     * <pre>array  $parametri["tabellej"]       array contenente tutte le tabelle per le quali richiedere
     *                                           la join a partire da $paricevuti[nometabella]</pre>
     * <pre>array  $parametri["escludere"]      array contenente tutti i campi che non devono essere restituiti</pre>
     * <pre>bool   $parametri["nospan"]         se true non imposta limit e offset</pre>
     * <pre>array  $parametri["parametri_link"] array contenente le colonne che devono essere rappresentate
     *                                           come dei link e relativi parametri per comporre l'href.
     *                                           Da non confondere con colonne_link che si passa a
     *                                           testataPerGriglia, perchè QUESTO array genera un
     *                                           tag <href> interno alla colonna per il quale si
     *                                           possono specificare le parti che lo compongono</pre>
     * <pre>array  $parametri["decodifiche"]    = array contenente eventuali decodifiche dei valori di
     *                                           una colonna che non può essere tradotta con una join
     *                                           ad una tabella</pre>
     * <pre>string $parametri["output"]         : "index" se i dati servono per la griglia dell'index, "stampa" se i dati servono per la stampa</pre>
     *
     * @return array in formato JSON con i dati richiesti
     */
    public function datiPerGriglia($parametri = [])
    {
        $request = $parametri['request'];
        $container = $parametri['container'];
        $doctrine = GrigliaParametriUtils::getDoctrineByEm($parametri);
        $bundle = $parametri['nomebundle'];
        $nometabella = $parametri['nometabella'];

        $gestionepermessi = $container->get('ficorebundle.gestionepermessi');
        $canRead = ($gestionepermessi->leggere(array('modulo' => $nometabella)) ? 1 : 0);
        if (!$canRead) {
            throw new AccessDeniedException("Non si hanno i permessi per visualizzare questo contenuto, controllare i permessi dell'utente");
        }

        $tabellej = GrigliaDatiUtils::getTabellejNormalizzate($parametri);

        $campiextra = GrigliaDatiUtils::getDatiCampiExtra($parametri);
        /* inserisco i filtri passati in un vettore */

        /* inserisco i parametri che sono passati nella $request all'interno di
          apposite variabili in che pagina siamo */
        /* direzione dell'ordinamento */
        $sord = $request->get('sord'); // get the direction if(!$sidx) $sidx =1;
        $page = $request->get('page'); // get the requested page
        /* quante righe restituire (in caso di nospan = false) */
        $limit = $request->get('rows'); // get how many rows we want to have into the grid
        /* su quale campo fare l'ordinamento */
        $sidx = $request->get('sidx'); // get index row - i.e. user click to sort
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

        GrigliaUtils::init();

        /* se ci sono delle precondizioni le imposta qui */
        GrigliaDatiPrecondizioniUtils::setPrecondizioni($q, $parametri);
        
        /* scorro ogni singola regola */
        $quanti = 0;
        GrigliaDatiMultiUtils::prepareQuery($parametri, $q, $sidx, $sord, $page, $limit, $quanti);

        $total_pages = GrigliaDatiMultiUtils::getTotalPages($quanti, $limit);

        /* imposta in $vettorerisposta la risposta strutturata per essere compresa da jqgrid */
        $vettorerisposta = array();
        $vettorerisposta['page'] = $page;
        $vettorerisposta['total'] = $total_pages;
        $vettorerisposta['records'] = $quanti;
        $vettorerisposta['filtri'] = json_decode($request->get('filters'), true);
        $indice = 0;

        $escludere = GrigliaParametriUtils::getDatiEscludere($parametri);
        $escludereutente = GrigliaParametriUtils::getDatiEscludereDaTabella($parametri);
        $ordinecolonne = GrigliaDatiUtils::getDatiOrdineColonne($parametri);
        $decodifiche = GrigliaDatiUtils::getDatiDecodifiche($parametri);

        $parametri["escludere"] = $escludere;
        $parametri["escludereutente"] = $escludereutente;
        $parametri["ordinecolonne"] = $ordinecolonne;
        $parametri["decodifiche"] = $decodifiche;
        $parametri["tabellej"] = $tabellej;

        /* Si scorrono tutti i records della query */
        foreach ($q as $singolo) {
            /* Si scorrono tutti i campi del record */
            $vettoreriga = array();
            $indicecolonna = 0;
            foreach ($singolo as $nomecampo => $singolocampo) {
                GrigliaDatiMultiUtils::buildDatiGriglia(
                    $parametri,
                    $vettoreriga,
                    $singolo,
                    $nomecampo,
                    $indice,
                    $indicecolonna,
                    $singolocampo
                );
            }

            GrigliaCampiExtraUtils::getCampiExtraDatiPerGriglia($campiextra, $vettoreriga, $doctrine, $entityName, $singolo);

            GrigliaDatiMultiUtils::buildRowGriglia($singolo, $vettoreriga, $vettorerisposta);
        }
        $response = new GridDati($vettorerisposta);
        return $response->getResponse();
    }
}

<?php

namespace Fi\CoreBundle\DependencyInjection;

use Doctrine\ORM\Tools\Pagination\Paginator;

class GrigliaDatiMultiUtils
{

    public static function getTotalPages($quanti, &$limit) 
    {
        /* calcola in mumero di pagine totali necessarie */
        return ceil($quanti / ($limit == 0 ? 1 : $limit));
    }

    public static function getLimit(&$limit) 
    {
        return ($limit ? $limit : 1);
    }

    public static function prepareQuery($parametri, &$q, &$sidx, &$sord, &$page, &$limit, &$quanti) 
    {
        $output = GrigliaUtils::getOuputType($parametri);
        $nospan = GrigliaDatiUtils::getDatiNospan($parametri);
        /* su quale campo fare l'ordinamento */
        /* conta il numero di record di risposta
          $query_tutti_records = $q->getQuery();
          $quanti = count($query_tutti_records->getSingleScalarResult()); */

        $paginator = new Paginator($q, true);
        $quanti = count($paginator);

        /* imposta l'offset, ovvero il record dal quale iniziare a visualizzare i dati */
        $offset = ($limit * ($page - 1));

        /* se si mandano i dati in stampa non tiene conto di limite e offset ovvero risponde con tutti i dati */
        if ($output != 'stampa') {
            /* se nospan non tiene conto di limite e offset ovvero risponde con tutti i dati */
            if (!($nospan)) {
                /* Imposta il limite ai record da estrarre */
                $q = ($limit ? $q->setMaxResults($limit) : $q);
                /* E imposta il primo record da visualizzare (per la paginazione) */
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
        /* Dall'oggetto querybuilder si ottiene la query da eseguire */
        $query_paginata = $q->getQuery();

        /* Object */
        /* $q = $query_paginata->getResult(); */
        /* Array */
        /* Si ottiene un array con tutti i records */
        $q = $query_paginata->getArrayResult();

        /* Se il limire non è stato impostato si mette 1 (per calcolare la paginazione) */
        $limit = GrigliaDatiMultiUtils::getLimit($limit);
    }

    public static function buildRowGriglia(&$singolo, &$vettoreriga, &$vettorerisposta) 
    {

        /* Si costruisce la risposta json per la jqgrid */
        ksort($vettoreriga);
        $vettorerigasorted = array();
        foreach ($vettoreriga as $value) {
            $vettorerigasorted[] = $value;
        }
        $vettorerisposta['rows'][] = array('id' => $singolo['id'], 'cell' => $vettorerigasorted);
        unset($vettoreriga);
    }

    public static function setOrdineColonneDatiGriglia(&$ordinecolonne, &$nomecampo, &$indice, &$indicecolonna) 
    {
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
    }

    public static function buildColonneDatiGriglia($parametri, &$vettoreriga, &$singolo, &$nomecampo, &$nomecampo, &$indice, &$indicecolonna, &$singolocampo) 
    {
        $doctrine = GrigliaUtils::getDoctrineByEm($parametri);
        /* $doctrineficore = GrigliaUtils::getDoctrineFiCoreByEm($paricevuti, $doctrine); */

        $bundle = $parametri['nomebundle'];
        $nometabella = $parametri['nometabella'];
        /* qui */
        $tabellej = GrigliaDatiUtils::getTabellejNormalizzate($parametri);

        $decodifiche = GrigliaDatiUtils::getDatiDecodifiche($parametri);
        $escludere = GrigliaDatiUtils::getDatiEscludere($parametri);
        $escludereutente = GrigliaDatiUtils::getDatiEscludere($parametri);
        $ordinecolonne = GrigliaDatiUtils::getDatiOrdineColonne($parametri);


        /* Si controlla se il campo è da escludere o meno */
        if ((!isset($escludere) || !(in_array($nomecampo, $escludere))) && (!isset($escludereutente) || !(in_array($nomecampo, $escludereutente)))) {
            if (isset($tabellej[$nomecampo])) {
                self::TabellejNomecampoNormalizzato($tabellej, $nomecampo);
                /* Per ogni campo si cattura il valore dall'array che torna doctrine */
                foreach ($tabellej[$nomecampo]['campi'] as $campoelencato) {
                    /* Object */
                    /* $fields = $singolo->get($tabellej[$nomecampo]["tabella"]) ? $singolo->get($tabellej[$nomecampo]["tabella"])->get($campoelencato) : ""; */
                    /* array */

                    GrigliaDatiMultiUtils::setOrdineColonneDatiGriglia($ordinecolonne, $nomecampo, $indice, $indicecolonna);

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

                GrigliaDatiMultiUtils::setOrdineColonneDatiGriglia($ordinecolonne, $nomecampo, $indice, $indicecolonna);

                GrigliaDatiUtils::valorizzaVettore($vettoreriga, array('singolocampo' => $singolocampo, 'tabella' => $bundle . ':' . $nometabella, 'nomecampo' => $nomecampo, 'doctrine' => $doctrine, 'ordinecampo' => $indicecolonna, 'decodifiche' => $decodifiche));
            }
        }
    }

    public static function TabellejNomecampoNormalizzato(&$tabellej, $nomecampo) 
    {
        if (is_object($tabellej[$nomecampo])) {
            $tabellej[$nomecampo] = get_object_vars($tabellej[$nomecampo]);
        }
    }

}

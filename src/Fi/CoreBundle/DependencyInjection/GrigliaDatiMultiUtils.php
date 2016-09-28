<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaDatiMultiUtils {

    public static function buildRowGriglia(&$singolo, &$vettoreriga, &$vettorerisposta) {

        /* Si costruisce la risposta json per la jqgrid */
        ksort($vettoreriga);
        $vettorerigasorted = array();
        foreach ($vettoreriga as $value) {
            $vettorerigasorted[] = $value;
        }
        $vettorerisposta['rows'][] = array('id' => $singolo['id'], 'cell' => $vettorerigasorted);
        unset($vettoreriga);
    }

    public static function setOrdineColonneDatiGriglia(&$ordinecolonne, &$nomecampo, &$indice, &$indicecolonna) {
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

    public static function buildColonneDatiGriglia($parametri, &$vettoreriga, &$singolo, &$nomecampo, &$nomecampo, &$indice, &$indicecolonna, &$singolocampo) {
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

    public static function TabellejNomecampoNormalizzato(&$tabellej, $nomecampo) {
        if (is_object($tabellej[$nomecampo])) {
            $tabellej[$nomecampo] = get_object_vars($tabellej[$nomecampo]);
        }
    }

}

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

}

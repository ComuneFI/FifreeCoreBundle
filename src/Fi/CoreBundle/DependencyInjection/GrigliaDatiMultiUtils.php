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

}

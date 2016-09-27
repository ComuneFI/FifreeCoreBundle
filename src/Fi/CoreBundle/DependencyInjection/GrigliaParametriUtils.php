<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaParametriUtils {

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

}

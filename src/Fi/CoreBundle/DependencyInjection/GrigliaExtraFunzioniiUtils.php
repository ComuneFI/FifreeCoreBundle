<?php

namespace Fi\CoreBundle\DependencyInjection;

use Fi\CoreBundle\Controller\GestionepermessiController;
use Fi\CoreBundle\Controller\FiUtilita;

class GrigliaExtraFunzioniiUtils {

    public static function getColonneLink($paricevuti, &$modellocolonne) {
        $output = GrigliaUtils::getOuputType($paricevuti);
        $colonne_link = isset($paricevuti['colonne_link']) ? $paricevuti['colonne_link'] : array();
        if (($output == 'stampa') || !isset($colonne_link)) {
            return;
        }

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

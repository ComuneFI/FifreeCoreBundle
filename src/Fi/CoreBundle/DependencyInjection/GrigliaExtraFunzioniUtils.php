<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaExtraFunzioniUtils
{
    public static function getColonneLink($paricevuti, &$modellocolonne)
    {
        $output = GrigliaParametriUtils::getOuputType($paricevuti);
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

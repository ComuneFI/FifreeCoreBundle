<?php

namespace Fi\CoreBundle\Utils;

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
    public static function valorizzaColonna(&$vettoreriga, $parametri)
    {
        $tabella = $parametri['tabella'];
        $nomecampo = $parametri['nomecampo'];
        $doctrine = $parametri['doctrine'];
        $decodifiche = $parametri['decodifiche'];

        $vettoreparcampi = $doctrine->getMetadataFactory()->getMetadataFor($tabella)->fieldMappings;

        if (is_object($vettoreparcampi)) {
            $vettoreparcampi = get_object_vars($vettoreparcampi);
        }

        $singolocampo = $parametri['singolocampo'];

        if (isset($decodifiche[$nomecampo]) && isset($decodifiche[$nomecampo][$singolocampo])) {
            $ordinecampo = $parametri['ordinecampo'];
            if (isset($ordinecampo)) {
                $vettoreriga[$ordinecampo] = $decodifiche[$nomecampo][$singolocampo];
            } else {
                $vettoreriga[] = $decodifiche[$nomecampo][$singolocampo];
            }
        } else {
            $vettoretype = isset($vettoreparcampi[$nomecampo]['type']) ? $vettoreparcampi[$nomecampo]['type'] : null;
            self::valorizzaVettoreType($vettoreparcampi, $vettoreriga, $vettoretype, $parametri);
        }
    }
    
    public static function valorizzaVettoreType(&$vettoreparcampi, &$vettoreriga, $vettoretype, $parametri)
    {
        $nomecampo = $parametri['nomecampo'];
        $ordinecampo = $parametri['ordinecampo'];
        $singolocampo = $parametri['singolocampo'];
        if (isset($vettoretype) && ($vettoretype == 'date' || $vettoretype == 'datetime') && $singolocampo) {
            if (isset($ordinecampo)) {
                $vettoreriga[$ordinecampo] = $singolocampo->format('d/m/Y');
            } else {
                $vettoreriga[] = $singolocampo->format('d/m/Y');
            }
        } elseif (isset($vettoretype) && ($vettoreparcampi[$nomecampo]['type'] == 'time') && $singolocampo) {
            if (isset($ordinecampo)) {
                $vettoreriga[$ordinecampo] = $singolocampo->format('H:i');
            } else {
                $vettoreriga[] = $singolocampo->format('H:i');
            }
        } else {
            if (isset($ordinecampo)) {
                $vettoreriga[$ordinecampo] = $singolocampo;
            } else {
                $vettoreriga[] = $singolocampo;
            }
        }
    }
}

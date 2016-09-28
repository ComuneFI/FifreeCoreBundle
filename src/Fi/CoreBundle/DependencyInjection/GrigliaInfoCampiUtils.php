<?php

namespace Fi\CoreBundle\DependencyInjection;

use Fi\CoreBundle\Controller\GestionepermessiController;
use Fi\CoreBundle\Controller\FiUtilita;

class GrigliaInfoCampiUtils
{

    public static function getEtichettaDescrizioneColonna(&$singoloalias, $chiave) 
    {
        return isset($singoloalias['descrizione']) ? $singoloalias['descrizione'] : GrigliaUtils::to_camel_case(array('str' => $chiave, 'primamaiuscola' => true));
    }

    public static function getEtichettaNomeColonna(&$etichetteutente, $chiave) 
    {
        return GrigliaUtils::to_camel_case(array('str' => trim($etichetteutente[$chiave]), 'primamaiuscola' => true));
    }

    public static function getWidthCampo(&$colonna, &$chiave, $singoloalias, $larghezzeutente) 
    {
        if ((isset($larghezzeutente[$chiave])) && ($larghezzeutente[$chiave] != '') && ($larghezzeutente[$chiave] != 0)) {
            $widthcampo = $larghezzeutente[$chiave];
        } else {
            $widthcampo = isset($singoloalias['lunghezza']) ? $singoloalias['lunghezza'] : ($colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA > GrigliaUtils::LARGHEZZAMASSIMA ? GrigliaUtils::LARGHEZZAMASSIMA : $colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA);
        }

        return $widthcampo;
    }

    public static function getIndiceModelloSelect(&$chiave, &$colonna, $singoloalias, $widthcampo) 
    {
        return array(
            'name' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave,
            'id' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave,
            'width' => $widthcampo,
            'tipocampo' => isset($singoloalias['tipo']) ? $singoloalias['tipo'] : $colonna['type'],
            'editable' => isset($singoloalias['editable']) ? $singoloalias['editable'] : null,
            'editoptions' => $singoloalias['valoricombo']);
    }

    public static function getIndiceModello(&$chiave, &$colonna, $singoloalias, $widthcampo) 
    {
        return array(
            'name' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave,
            'id' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave,
            'width' => $widthcampo,
            'tipocampo' => isset($singoloalias['tipo']) ? $singoloalias['tipo'] : $colonna['type'],
            'editable' => isset($singoloalias['editable']) ? $singoloalias['editable'] : null
        );
    }

    public static function setModelliColonne(&$modellocolonne, &$colonna, &$chiave, &$singoloalias, &$indicecolonna, $larghezzeutente) 
    {
        $widthcampo = self::getWidthCampo($colonna, $chiave, $singoloalias, $larghezzeutente);

        if (isset($singoloalias['tipo']) && ($singoloalias['tipo'] == 'select')) {
            $modellocolonne[$indicecolonna] = self::getIndiceModelloSelect($chiave, $colonna, $singoloalias, $widthcampo);
        } else {
            $modellocolonne[$indicecolonna] = self::getIndiceModello($chiave, $colonna, $singoloalias, $widthcampo);
        }
    }

    public static function setNomiColonne(&$nomicolonne, &$chiave, &$singoloalias, &$indicecolonna, &$etichetteutente) 
    {
        if ((isset($etichetteutente[$chiave])) && (trim($etichetteutente[$chiave]) != '')) {
            $nomicolonne[$indicecolonna] = self::getEtichettaNomeColonna($etichetteutente, $chiave);
        } else {
            $nomicolonne[$indicecolonna] = self::getEtichettaDescrizioneColonna($singoloalias, $chiave);
        }
    }

    public static function getOrdineColonne(&$chiave, &$indice, $ordinecolonne, &$indicecolonna) 
    {
        $indicecolonna = array_search($chiave, $ordinecolonne);
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
    }

    public static function getSingoloAliasNormalizzato(&$singoloalias) 
    {
        if (is_object($singoloalias)) {
            $singoloalias = get_object_vars($singoloalias);
        }
    }

    public static function setOrdineColonne(&$ordinecolonne, &$chiave, &$indice, &$indicecolonna, &$ordinecolonne) 
    {
        if (isset($ordinecolonne)) {
            GrigliaInfoCampiUtils::getOrdineColonne($chiave, $indice, $ordinecolonne, $indicecolonna);
        } else {
            ++$indice;
            $indicecolonna = $indice;
        }
    }

}

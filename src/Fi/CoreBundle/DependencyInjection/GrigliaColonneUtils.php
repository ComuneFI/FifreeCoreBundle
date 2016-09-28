<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaColonneUtils
{
    public static function getColonne(&$nomicolonne, &$modellocolonne, &$indice, $paricevuti)
    {
        $doctrine = GrigliaUtils::getDoctrineByEm($paricevuti);

        $nometabella = $paricevuti['nometabella'];
        $bundle = $paricevuti['nomebundle'];
        $entityName = $bundle.':'.$nometabella;

        $colonne = self::getColonneDatabase(array('entityName' => $entityName, 'doctrine' => $doctrine));

        foreach ($colonne as $chiave => $colonna) {
            self::elaboraColonna($chiave, $colonna, $nomicolonne, $modellocolonne, $indice, $paricevuti);
        }
    }

    public static function elaboraColonna(&$chiave, &$colonna, &$nomicolonne, &$modellocolonne, &$indice, $paricevuti)
    {
        $alias = GrigliaParametriUtils::getAliasTestataPerGriglia($paricevuti);
        $escludere = GrigliaParametriUtils::getCampiEsclusiTestataPerGriglia($paricevuti);
        $escludereutente = GrigliaRegoleUtils::campiesclusi($paricevuti);

        if ((!isset($escludere) || !(in_array($chiave, $escludere))) && (!isset($escludereutente) || !(in_array($chiave, $escludereutente)))) {
            if (isset($alias[$chiave])) {
                self::getAliasCampi($nomicolonne, $modellocolonne, $indice, $chiave, $colonna, $paricevuti);
            } else {
                self::getDettagliCampi($nomicolonne, $modellocolonne, $alias, $indice, $chiave, $colonna, $paricevuti);
            }
        }
    }

    public static function getColonneDatabase($parametri = array())
    {
        $entityName = $parametri['entityName'];
        /* @var $doctrine \Doctrine\ORM\EntityManager */
        $doctrine = $parametri['doctrine'];

        /* $infocolonne = $doctrine->getClassMetadata($entityName)->getColumnNames(); */
        $infocolonne = $doctrine->getMetadataFactory()->getMetadataFor($entityName);
        /* $infocolonne = get_object_vars($infocolonne); */

        foreach ($infocolonne->fieldMappings as $colonna) {
            /* getFieldMapping */
            /* $ret = $doctrine->getMetadataFactory()->getMetadataFor($entityName)->; */
            $colonne[$colonna['fieldName']] = $colonna;

            /* $colonne[$colonna] = $doctrine->getClassMetadata($entityName)->getTypeOfField($colonna);
              $colonne[$colonna] = $doctrine->getClassMetadata($entityName)->getColumnName($colonna);
              $colonne[$colonna] = $doctrine->getClassMetadata($entityName)->getFieldForColumn($colonna);
              $colonne[$colonna] = $doctrine->getClassMetadata($entityName)->getTypeOfColumn($colonna);
              $colonne[$colonna] = $doctrine->getClassMetadata($entityName)->getColumnNames();
             */
            if ($colonne[$colonna['fieldName']]['type'] == 'integer' || !(isset($colonne[$colonna['fieldName']]['length']))) {
                $colonne[$colonna['fieldName']]['length'] = 11;
            }
        }

        return $colonne;
    }

    public static function getColonneOrdinate($ordine)
    {
        $ordinecolonne = null;

        if (count($ordine) > 0) {
            $ordinecolonne = array();
            foreach ($ordine as $value) {
                $ordinecolonne[] = $value;
            }
        }

        return $ordinecolonne;
    }

    public static function getAliasCampi(&$nomicolonne, &$modellocolonne, &$indice, &$chiave, &$colonna, $paricevuti)
    {
        $alias = GrigliaParametriUtils::getAliasTestataPerGriglia($paricevuti);
        $etichetteutente = GrigliaUtils::etichettecampi($paricevuti);
        $larghezzeutente = GrigliaUtils::larghezzecampi($paricevuti);
        $ordinecolonne = GrigliaParametriUtils::getOrdineColonneTestataPerGriglia($paricevuti);

        $moltialias = (isset($alias[$chiave]) ? $alias[$chiave] : null);

        $indicecolonna = 0;

        foreach ($moltialias as $singoloalias) {
            GrigliaInfoCampiUtils::setOrdineColonne($ordinecolonne, $chiave, $indice, $indicecolonna, $ordinecolonne);

            GrigliaInfoCampiUtils::getSingoloAliasNormalizzato($singoloalias);

            GrigliaInfoCampiUtils::setNomiColonne($nomicolonne, $chiave, $singoloalias, $indicecolonna, $etichetteutente);

            GrigliaInfoCampiUtils::setModelliColonne($modellocolonne, $colonna, $chiave, $singoloalias, $indicecolonna, $larghezzeutente);
        }
    }

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
            'editoptions' => $singoloalias['valoricombo'], );
    }

    public static function getIndiceModello(&$chiave, &$colonna, $singoloalias, $widthcampo)
    {
        return array(
            'name' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave,
            'id' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave,
            'width' => $widthcampo,
            'tipocampo' => isset($singoloalias['tipo']) ? $singoloalias['tipo'] : $colonna['type'],
            'editable' => isset($singoloalias['editable']) ? $singoloalias['editable'] : null,
        );
    }

    public static function getDettagliCampi(&$nomicolonne, &$modellocolonne, &$alias, &$indice, &$chiave, &$colonna, &$paricevuti)
    {
        $etichetteutente = GrigliaUtils::etichettecampi($paricevuti);
        $larghezzeutente = GrigliaUtils::larghezzecampi($paricevuti);
        $ordinecolonne = GrigliaParametriUtils::getOrdineColonneTestataPerGriglia($paricevuti);

        $indicecolonna = 0;

        GrigliaInfoCampiUtils::setOrdineColonne($ordinecolonne, $chiave, $indice, $indicecolonna, $ordinecolonne);

        GrigliaInfoCampiUtils::setNomiColonne($nomicolonne, $chiave, $alias, $indicecolonna, $etichetteutente);

        if ((isset($larghezzeutente[$chiave])) && ($larghezzeutente[$chiave] != '') && ($larghezzeutente[$chiave] != 0)) {
            $widthcampo = $larghezzeutente[$chiave];
        } else {
            $widthcampo = ($colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA > GrigliaUtils::LARGHEZZAMASSIMA ? GrigliaUtils::LARGHEZZAMASSIMA : $colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA);
        }

        $modellocolonne[$indicecolonna] = array('name' => $chiave, 'id' => $chiave, 'width' => $widthcampo, 'tipocampo' => $colonna['type']);
    }
}

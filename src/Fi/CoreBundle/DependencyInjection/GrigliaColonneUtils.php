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
            GrigliaInfoCampiUtils::setOrdineColonne($ordinecolonne, $chiave, $indice, $indicecolonna);

            GrigliaInfoCampiUtils::getSingoloAliasNormalizzato($singoloalias);

            GrigliaInfoCampiUtils::setNomiColonne($nomicolonne, $chiave, $singoloalias, $indicecolonna, $etichetteutente);

            GrigliaInfoCampiUtils::setModelliColonne($modellocolonne, $colonna, $chiave, $singoloalias, $indicecolonna, $larghezzeutente);
        }
    }

    public static function getEtichettaDescrizioneColonna(&$singoloalias, $chiave)
    {
        $parametri = array('str' => $chiave, 'primamaiuscola' => true);

        return isset($singoloalias['descrizione']) ? $singoloalias['descrizione'] : GrigliaUtils::toCamelCase($parametri);
    }

    public static function getEtichettaNomeColonna(&$etichetteutente, $chiave)
    {
        return GrigliaUtils::toCamelCase(array('str' => trim($etichetteutente[$chiave]), 'primamaiuscola' => true));
    }

    public static function getWidthCampo(&$colonna, &$chiave, $singoloalias, $larghezzeutente)
    {
        if ((isset($larghezzeutente[$chiave])) && ($larghezzeutente[$chiave] != '') && ($larghezzeutente[$chiave] != 0)) {
            $widthcampo = $larghezzeutente[$chiave];
        } else {
            $moltiplicatorelarghezza = GrigliaUtils::MOLTIPLICATORELARGHEZZA;
            $larghezzamassima = GrigliaUtils::LARGHEZZAMASSIMA;
            $singoloaliaslunghezza = $singoloalias['lunghezza'];
            $moltiplicatore = $colonna['length'] * $moltiplicatorelarghezza;
            $larghezzacalc = $colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA;
            $larghezzaricalcolata = ($moltiplicatore > $larghezzamassima ? $larghezzamassima : $larghezzacalc);

            $widthcampo = isset($singoloaliaslunghezza) ? $singoloaliaslunghezza : $larghezzaricalcolata;
        }

        return $widthcampo;
    }

    public static function getDettagliCampi(&$nomicolonne, &$modellocolonne, &$alias, &$indice, &$chiave, &$colonna, &$paricevuti)
    {
        $etichetteutente = GrigliaUtils::etichettecampi($paricevuti);
        $larghezzeutente = GrigliaUtils::larghezzecampi($paricevuti);
        $ordinecolonne = GrigliaParametriUtils::getOrdineColonneTestataPerGriglia($paricevuti);

        $indicecolonna = 0;

        GrigliaInfoCampiUtils::setOrdineColonne($ordinecolonne, $chiave, $indice, $indicecolonna);

        GrigliaInfoCampiUtils::setNomiColonne($nomicolonne, $chiave, $alias, $indicecolonna, $etichetteutente);

        if ((isset($larghezzeutente[$chiave])) && ($larghezzeutente[$chiave] != '') && ($larghezzeutente[$chiave] != 0)) {
            $widthcampo = $larghezzeutente[$chiave];
        } else {
            $widthcampo = ($colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA > GrigliaUtils::LARGHEZZAMASSIMA ?
                            GrigliaUtils::LARGHEZZAMASSIMA :
                            $colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA);
        }

        $modellocolonne[$indicecolonna] = array('name' => $chiave, 'id' => $chiave, 'width' => $widthcampo, 'tipocampo' => $colonna['type']);
    }
}

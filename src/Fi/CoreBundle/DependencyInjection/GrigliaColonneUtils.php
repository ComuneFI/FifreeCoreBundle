<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaColonneUtils {

    public static function getColonne(&$nomicolonne, &$modellocolonne, &$indice, $paricevuti) {
        $alias = GrigliaParametriUtils::getAliasTestataPerGriglia($paricevuti);
        $escludere = GrigliaParametriUtils::getCampiEsclusiTestataPerGriglia($paricevuti);
        $escludereutente = GrigliaRegoleUtils::campiesclusi($paricevuti);
        $etichetteutente = GrigliaUtils::etichettecampi($paricevuti);
        $larghezzeutente = GrigliaUtils::larghezzecampi($paricevuti);
        $ordinecolonne = GrigliaParametriUtils::getOrdineColonneTestataPerGriglia($paricevuti);

        $doctrine = GrigliaUtils::getDoctrineByEm($paricevuti);

        $nometabella = $paricevuti['nometabella'];
        $bundle = $paricevuti['nomebundle'];
        $entityName = $bundle . ':' . $nometabella;

        $colonne = GrigliaColonneUtils::getColonneDatabase(array('entityName' => $entityName, 'doctrine' => $doctrine));

        foreach ($colonne as $chiave => $colonna) {
            if ((!isset($escludere) || !(in_array($chiave, $escludere))) && (!isset($escludereutente) || !(in_array($chiave, $escludereutente)))) {
                $moltialias = (isset($alias[$chiave]) ? $alias[$chiave] : null);

                if (isset($alias[$chiave])) {
                    foreach ($moltialias as $singoloalias) {
                        if (isset($ordinecolonne)) {
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
                        } else {
                            ++$indice;
                            $indicecolonna = $indice;
                        }

                        if (is_object($singoloalias)) {
                            $singoloalias = get_object_vars($singoloalias);
                        }

                        if ((isset($etichetteutente[$chiave])) && (trim($etichetteutente[$chiave]) != '')) {
                            $nomicolonne[$indicecolonna] = GrigliaUtils::to_camel_case(array('str' => trim($etichetteutente[$chiave]), 'primamaiuscola' => true));
                        } else {
                            $nomicolonne[$indicecolonna] = isset($singoloalias['descrizione']) ? $singoloalias['descrizione'] : GrigliaUtils::to_camel_case(array('str' => $chiave, 'primamaiuscola' => true));
                        }

                        if ((isset($larghezzeutente[$chiave])) && ($larghezzeutente[$chiave] != '') && ($larghezzeutente[$chiave] != 0)) {
                            $widthcampo = $larghezzeutente[$chiave];
                        } else {
                            $widthcampo = isset($singoloalias['lunghezza']) ? $singoloalias['lunghezza'] : ($colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA > GrigliaUtils::LARGHEZZAMASSIMA ? GrigliaUtils::LARGHEZZAMASSIMA : $colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA);
                        }

                        if (isset($singoloalias['tipo']) && ($singoloalias['tipo'] == 'select')) {
                            $modellocolonne[$indicecolonna] = array('name' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave, 'id' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave, 'width' => $widthcampo, 'tipocampo' => isset($singoloalias['tipo']) ? $singoloalias['tipo'] : $colonna['type'], 'editable' => isset($singoloalias['editable']) ? $singoloalias['editable'] : null, 'editoptions' => $singoloalias['valoricombo']);
                        } else {
                            $modellocolonne[$indicecolonna] = array('name' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave, 'id' => isset($singoloalias['nomecampo']) ? $singoloalias['nomecampo'] : $chiave, 'width' => $widthcampo, 'tipocampo' => isset($singoloalias['tipo']) ? $singoloalias['tipo'] : $colonna['type'], 'editable' => isset($singoloalias['editable']) ? $singoloalias['editable'] : null);
                        }
                    }
                } else {
                    if (isset($ordinecolonne)) {
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
                    } else {
                        ++$indice;
                        $indicecolonna = $indice;
                    }
                    if ((isset($etichetteutente[$chiave])) && (trim($etichetteutente[$chiave]) != '')) {
                        $nomicolonne[$indicecolonna] = GrigliaUtils::to_camel_case(array('str' => trim($etichetteutente[$chiave]), 'primamaiuscola' => true));
                    } else {
                        $nomicolonne[$indicecolonna] = GrigliaUtils::to_camel_case(array('str' => $chiave, 'primamaiuscola' => true));
                    }

                    if ((isset($larghezzeutente[$chiave])) && ($larghezzeutente[$chiave] != '') && ($larghezzeutente[$chiave] != 0)) {
                        $widthcampo = $larghezzeutente[$chiave];
                    } else {
                        $widthcampo = ($colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA > GrigliaUtils::LARGHEZZAMASSIMA ? GrigliaUtils::LARGHEZZAMASSIMA : $colonna['length'] * GrigliaUtils::MOLTIPLICATORELARGHEZZA);
                    }

                    $modellocolonne[$indicecolonna] = array('name' => $chiave, 'id' => $chiave, 'width' => $widthcampo, 'tipocampo' => $colonna['type']);
                }
            }
        }
    }

    public static function getColonneDatabase($parametri = array()) {
        $entityName = $parametri['entityName'];
        /* @var $doctrine \Doctrine\ORM\EntityManager */
        $doctrine = $parametri['doctrine'];

        //$infocolonne = $doctrine->getClassMetadata($entityName)->getColumnNames();
        $infocolonne = $doctrine->getMetadataFactory()->getMetadataFor($entityName);
        //$infocolonne = get_object_vars($infocolonne);

        foreach ($infocolonne->fieldMappings as $colonna) {
            //getFieldMapping
            //$doctrine->getConnection()->getSchemaManager()->
            //$ret = $doctrine->getMetadataFactory()->getMetadataFor($entityName)->;
            //if ($colonna == 'descrizione' ){
            $colonne[$colonna['fieldName']] = $colonna;
            //}

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

}

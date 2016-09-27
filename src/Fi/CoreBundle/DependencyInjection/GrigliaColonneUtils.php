<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaColonneUtils {

    public static function getColonne(&$nomicolonne, &$modellocolonne, &$indice, $colonne, $ordinecolonne, $escludere, $escludereutente, $alias, $etichetteutente, $larghezzeutente) {
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
                            $widthcampo = isset($singoloalias['lunghezza']) ? $singoloalias['lunghezza'] : ($colonna['length'] * GrigliaUtils::Moltiplicatorelarghezza > GrigliaUtils::Larghezzamassima ? GrigliaUtils::Larghezzamassima : $colonna['length'] * GrigliaUtils::Moltiplicatorelarghezza);
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
                        $widthcampo = ($colonna['length'] * GrigliaUtils::Moltiplicatorelarghezza > GrigliaUtils::Larghezzamassima ? GrigliaUtils::Larghezzamassima : $colonna['length'] * GrigliaUtils::Moltiplicatorelarghezza);
                    }

                    $modellocolonne[$indicecolonna] = array('name' => $chiave, 'id' => $chiave, 'width' => $widthcampo, 'tipocampo' => $colonna['type']);
                }
            }
        }
    }

}

<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaCampiExtraUtils
{

    public static function getCampiExtraNormalizzati(&$campiextra)
    {
        /* Se è un array di una dimensione si trasforma in bidimensionale */
        if (count($campiextra) == count($campiextra, \COUNT_RECURSIVE)) {
            $campoextraarray = $campiextra;
            $campiextra = array();
            foreach ($campoextraarray as $campoextranormalize) {
                if (is_object($campoextranormalize)) {
                    $campoextranormalize = get_object_vars($campoextranormalize);
                    $campiextra[] = $campoextranormalize;
                }
            }
            if (count($campiextra) == 0) {
                $campiextra = $campoextraarray;
            }
        }
    }

    public static function getCampiExtraTestataPerGriglia($paricevuti, &$indice, &$nomicolonne, &$modellocolonne)
    {
        $campiextra = GrigliaParametriUtils::getParametriCampiExtraTestataPerGriglia($paricevuti);
        if (!isset($campiextra)) {
            return;
        }
        self::getCampiExtraNormalizzati($campiextra);

        foreach ($campiextra as $chiave => $colonna) {
            ++$indice;
            self::getCampiExtraColonneNormalizzate($colonna);

            $nomicolonne[$indice] = self::getCampiExtraNomiColonne($colonna, $chiave);

            $widthcolonna = self::getCampiExtraWidthColonna($colonna);

            $tipocolonna = self::getCampiExtraTipoColonna($colonna);
            $idcolonna = self::getCampiExtraNomeCampoColonna($colonna, $chiave);
            $nomecolonna = self::getCampiExtraIdCampoColonna($colonna, $chiave);

            $modellocolonne[$indice] = array(
                'name' => $nomecolonna,
                'id' => $idcolonna,
                'width' => $widthcolonna,
                'tipocampo' => $tipocolonna,
                'search' => false,);
        }
    }

    public static function getCampiExtraIdCampoColonna($colonna, $chiave)
    {
        return isset($colonna['nomecampo']) ? $colonna['nomecampo'] : $chiave;
    }

    public static function getCampiExtraTipoColonna($colonna)
    {
        return isset($colonna['tipo']) ? $colonna['tipo'] : $colonna['type'];
    }

    public static function getCampiExtraNomeCampoColonna($colonna, $chiave)
    {
        return isset($colonna['nomecampo']) ? $colonna['nomecampo'] : $chiave;
    }

    public static function getCampiExtraNomiColonne(&$colonna, $chiave)
    {
        $parametri = array('str' => $chiave, 'primamaiuscola' => true);

        return isset($colonna['descrizione']) ? $colonna['descrizione'] : GrigliaUtils::toCamelCase($parametri);
    }

    public static function getCampiExtraWidthColonna($colonna)
    {
        $width = isset($colonna['lunghezza']) ? $colonna['lunghezza'] : GrigliaUtils::LARGHEZZAMASSIMA;

        return $width;
    }

    public static function getCampiExtraColonneNormalizzate(&$colonna)
    {
        if (is_object($colonna)) {
            $colonna = get_object_vars($colonna);
        }
    }

    public static function getCampiExtraDatiPerGriglia(&$campiextra, &$vettoreriga, $doctrine, $entityName, $singolo)
    {
        /* Gestione per passare campi che non sono nella tabella ma metodi del model (o richiamabili tramite magic method get) */
        if (isset($campiextra)) {
            GrigliaCampiExtraUtils::getCampiExtraNormalizzati($campiextra);
            foreach ($campiextra as $vettore) {
                foreach ($vettore as $singolocampo) {
                    $campo = 'get' . ucfirst($singolocampo);
                    /* @var $doctrine \Doctrine\ORM\EntityManager */
                    $objTabella = $doctrine->find($entityName, $singolo['id']);
                    $vettoreriga[] = $objTabella->$campo();
                }
            }
        }
    }
}

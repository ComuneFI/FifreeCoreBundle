<?php

namespace Fi\CoreBundle\DependencyInjection;

use Fi\CoreBundle\Controller\GestionepermessiController;
use Fi\CoreBundle\Controller\FiUtilita;

class GrigliaRegoleUtils
{
    public static function getTipoRegola(&$tipo, &$regola, $parametri)
    {
        $doctrine = $parametri['doctrine'];
        $nometabella = $parametri['nometabella'];
        $entityName = $parametri['entityName'];
        $bundle = $parametri['bundle'];

        $elencocampi = $doctrine->getClassMetadata($entityName)->getFieldNames();

        if (strrpos($regola['field'], '.') == 0) {
            if (in_array($regola['field'], $elencocampi) == true) {
                $type = $doctrine->getClassMetadata($entityName)->getFieldMapping($regola['field']);
                $tipo = $type['type'];

                //Si aggiunge l'alias al campo altrimenti da Doctrine2 fallisce la query
                $regola['field'] = $nometabella.'.'.$regola['field'];
            }
        } else {
            //Altrimenti stiamo analizzando il campo di una tabella in leftjoin pertanto si cercano le informazioni sul tipo
            //dei campi nella tabella "joinata"
            $tablejoined = substr($regola['field'], 0, strrpos($regola['field'], '.'));
            $fieldjoined = substr($regola['field'], strrpos($regola['field'], '.') + 1);

            $entityNametablejoined = $bundle.':'.$tablejoined;

            $type = $doctrine->getClassMetadata($entityNametablejoined)->getFieldMapping($fieldjoined);
            $tipo = $type['type'];
        }
    }

    public static function getRegolaPerData(&$regola)
    {
        if (isset($regola) && count($regola) > 0) {
            if ((substr($regola['data'], 0, 1) == "'") && (substr($regola['data'], strlen($regola['data']) - 1, 1) == "'")) {
                $regola['data'] = substr($regola['data'], 1, strlen($regola['data']) - 2);
            }
        }
    }

    public static function setRegole(&$q, &$primo, $parametri = array())
    {
        $regole = $parametri['regole'];
        $tipof = $parametri['tipof'];
        $tipo = null;
        foreach ($regole as $regola) {
            //Se il campo non ha il . significa che è necessario aggiungere il nometabella
            self::getTipoRegola($tipo, $regola, $parametri);

            $regola = self::setSingolaRegola($tipo, $regola);
            if (!$regola) {
                continue;
            }
            if ($tipof == 'OR') {
                $condizioneOR = $regola['field'].' '.
                        GrigliaUtils::$decodificaop[$regola['op']].' '.
                        GrigliaUtils::$precarattere[$regola['op']].
                        $regola['data'].
                        GrigliaUtils::$postcarattere[$regola['op']];
                $q->orWhere($condizioneOR);
            } else {
                $condizioneAND = $regola['field'].' '.
                        GrigliaUtils::$decodificaop[$regola['op']].' '.
                        GrigliaUtils::$precarattere[$regola['op']].
                        $regola['data'].
                        GrigliaUtils::$postcarattere[$regola['op']];
                $q->andWhere($condizioneAND);
            }
        }
    }

    public static function setSingolaRegola($tipo, $regola)
    {
        if (!$tipo) {
            GrigliaUtils::setVettoriPerNumero();
        }
        if ($tipo == 'date' || $tipo == 'datetime') {
            GrigliaUtils::setVettoriPerData();
            $regola['data'] = FiUtilita::data2db($regola['data']);
        } elseif ($tipo == 'string') {
            GrigliaUtils::setVettoriPerStringa();
            $regola['field'] = 'lower('.$regola['field'].')';
        }
        if ($tipo == 'boolean') {
            self::setTipoBoolean($regola, $tipo);
        }

        self::getRegolaPerData($regola);

        return $regola;
    }

    public static function setTipoBoolean(&$regola, $tipo)
    {
        if ($regola['data'] === 'false' || $regola['data'] === false) {
            GrigliaUtils::setVettoriPerBoolean();
            $regola['op'] = 'eq';
            $regola['data'] = 0;
        }
        if ($regola['data'] === 'true' || $regola['data'] === true) {
            GrigliaUtils::setVettoriPerBoolean();
            $regola['op'] = 'eq';
            $regola['data'] = 1;
        }
        if ($tipo == 'boolean' && $regola['data'] == 'null') {
            $regola = array();
        }
    }

    public static function campiesclusi($parametri = array())
    {
        if (!isset($parametri['nometabella'])) {
            return false;
        }

        $output = GrigliaParametriUtils::getOuputType($parametri);

        $nometabella = $parametri['nometabella'];

        $doctrine = GrigliaParametriUtils::getDoctrineByEm($parametri);
        $doctrineficore = GrigliaParametriUtils::getDoctrineFiCoreByEm($parametri, $doctrine);

        $gestionepermessi = new GestionepermessiController($parametri['container']);
        $operatorecorrente = $gestionepermessi->utentecorrenteAction();

        $escludi = array();
        $q = GrigliaUtils::getUserCustomTableFields($doctrineficore, $nometabella, $operatorecorrente);
        if (!$q) {
            return $escludi;
        }

        foreach ($q as $riga) {
            $campo = GrigliaUtils::getCampiEsclusi($riga, $output);
            if ($campo) {
                $escludi[] = $campo;
            }
        }

        return $escludi;
    }
}

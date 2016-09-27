<?php

namespace Fi\CoreBundle\DependencyInjection;

use Fi\CoreBundle\Controller\GestionepermessiController;
use Fi\CoreBundle\Controller\FiUtilita;

class GrigliaRegoleUtils {

    public static function getTipoRegola($regola, $parametri) {
        $doctrine = $parametri['doctrine'];
        $nometabella = $parametri['nometabella'];
        $entityName = $parametri['entityName'];
        $bundle = $parametri['bundle'];

        $elencocampi = $doctrine->getClassMetadata($entityName)->getFieldNames();
        $tipo = null;
        if (strrpos($regola['field'], '.') == 0) {
            if (in_array($regola['field'], $elencocampi) == true) {
                $type = $doctrine->getClassMetadata($entityName)->getFieldMapping($regola['field']);
                $tipo = $type['type'];

                //Si aggiunge l'alias al campo altrimenti da Doctrine2 fallisce la query
                $regola['field'] = $nometabella . '.' . $regola['field'];
            }
        } else {
            //Altrimenti stiamo analizzando il campo di una tabella in leftjoin pertanto si cercano le informazioni sul tipo
            //dei campi nella tabella "joinata"
            $tablejoined = substr($regola['field'], 0, strrpos($regola['field'], '.'));
            $fieldjoined = substr($regola['field'], strrpos($regola['field'], '.') + 1);

            $entityNametablejoined = $bundle . ':' . $tablejoined;

            $type = $doctrine->getClassMetadata($entityNametablejoined)->getFieldMapping($fieldjoined);
            $tipo = $type['type'];
        }
        return $tipo;
    }

    public static function getRegolaPerData($regola) {
        if ((substr($regola['data'], 0, 1) == "'") && (substr($regola['data'], strlen($regola['data']) - 1, 1) == "'")) {
            $regola['data'] = substr($regola['data'], 1, strlen($regola['data']) - 2);
        }

        return $regola;
    }

    public static function setRegole(&$q, &$primo, $parametri = array()) {
        $regole = $parametri['regole'];
        $tipof = $parametri['tipof'];

        foreach ($regole as $regola) {
            //Se il campo non ha il . significa che è necessario aggiungere il nometabella
            $tipo = GrigliaRegoleUtils::getTipoRegola($regola, $parametri);

            $regola = self::setSingolaRegola($tipo, $regola);
            if (!$regola) {
                continue;
            }

            if ($tipof == 'OR') {
                $q->orWhere($regola['field'] . ' ' . GrigliaUtils::$decodificaop[$regola['op']] . ' ' . GrigliaUtils::$precarattere[$regola['op']] . $regola['data'] . GrigliaUtils::$postcarattere[$regola['op']]);
            } else {
                $q->andWhere($regola['field'] . ' ' . GrigliaUtils::$decodificaop[$regola['op']] . ' ' . GrigliaUtils::$precarattere[$regola['op']] . $regola['data'] . GrigliaUtils::$postcarattere[$regola['op']]);
            }
        }
    }

    public static function setSingolaRegola($tipo, $regola) {
        if (!$tipo) {
            GrigliaUtils::setVettoriPerNumero();
        } else {
            if ($tipo == 'date' || $tipo == 'datetime') {
                GrigliaUtils::setVettoriPerData();
                $regola['data'] = FiUtilita::data2db($regola['data']);
            } elseif ($tipo == 'string') {
                GrigliaUtils::setVettoriPerStringa();
                $regola['field'] = 'lower(' . $regola['field'] . ')';
            }

            if (($tipo == 'boolean') && ($regola['data'] === 'false' || $regola['data'] === false)) {
                $regola['op'] = 'eq';
                $regola['data'] = 0;
            }
            if (($tipo == 'boolean') && ($regola['data'] === 'true' || $regola['data'] === true)) {
                $regola['op'] = 'eq';
                $regola['data'] = 1;
            }
        }
        $regolareturn = GrigliaRegoleUtils::getRegolaPerData($regola);
        return $regolareturn;
    }

}

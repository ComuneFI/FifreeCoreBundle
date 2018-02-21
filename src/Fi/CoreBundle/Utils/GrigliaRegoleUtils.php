<?php

namespace Fi\CoreBundle\Utils;

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
            if (in_array($regola['field'], $elencocampi) === true) {
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
    }

    public static function getRegolaPerData(&$regola)
    {
        if (isset($regola) && count($regola) > 0) {
            $regola['data'] = preg_replace('/^(\'(.*)\'|"(.*)")$/', '$2$3', $regola['data']);
        }
    }

    public static function setRegole(&$q, &$primo, $parametri = array())
    {
        $regole = $parametri['regole'];
        //file_put_contents("/tmp/appo.log", print_r($regole, true) . "\n", FILE_APPEND);
        //$tipof = $parametri['tipof'];
        $tipo = null;
        //file_put_contents("/tmp/appo.log", "\n\n" .  . "\n\n", FILE_APPEND);
        foreach ($regole as $key => $regola) {
            //file_put_contents("/tmp/appo.log", "***1***" . "\n", FILE_APPEND);
            //Se il campo non ha il . significa che è necessario aggiungere il nometabella
            self::getTipoRegola($tipo, $regola, $parametri);
            $tipof = isset($regola["typof"]) ? $regola["typof"] : 'AND';
            $regola = self::setSingolaRegola($tipo, $regola);
            if (!$regola) {
                continue;
            }

            $conditions = self::getDoctrineConditions($regola, $key);

            $condition = $conditions["condition"];
            $sqlparameter = $conditions["sqlparameter"];
            $value = $conditions["value"];

            if ($primo) {
                $primo = false;
                $q->where($condition);
            } else {
                //file_put_contents("/tmp/appo.log", $tipof . "\n", FILE_APPEND);
                if (strtoupper($tipof) == 'OR') {
                    $q->orWhere($condition);
                } else {
                    $q->andwhere($condition);
                }
            }
            if ($sqlparameter) {
                $q->setParameter($sqlparameter, $value);
            }
            /* $condizioneAND = $regola['field'] . ' ' .
              GrigliaUtils::$decodificaop[$regola['op']] . ' ' .
              GrigliaUtils::$precarattere[$regola['op']] .
              str_replace("'", "''", $regola['data']) .
              GrigliaUtils::$postcarattere[$regola['op']];
              $q->andWhere($condizioneAND); */
            //file_put_contents("/tmp/appo.log", dump($fieldparm) . "\n", FILE_APPEND);
            //file_put_contents("/tmp/appo.log", dump($sqlparameter) . "\n", FILE_APPEND);
            //file_put_contents("/tmp/appo.log", print_r($condition, true) . "\n", FILE_APPEND);
            //file_put_contents("/tmp/appo.log", print_r($value, true) . "\n", FILE_APPEND);
        }
    }

    private static function getDoctrineConditions($regola, $key)
    {
        //file_put_contents("/tmp/appo.log", "!" . $tipo . "! " . print_r($regola,true) . "\n", FILE_APPEND);
        if (GrigliaUtils::$decodificaop[$regola['op']] == 'IS' || GrigliaUtils::$decodificaop[$regola['op']] == 'IS NOT') {
            $condition = GrigliaUtils::$precaratterecampo[$regola['op']]
                    . $regola['field']
                    . GrigliaUtils::$postcaratterecampo[$regola['op']] . " "
                    . GrigliaUtils::$decodificaop[$regola['op']] . " NULL";
            $value = null;
            $sqlparameter = null;
            return array("condition" => $condition, "sqlparameter" => $sqlparameter, "value" => $value);
        }

        if (GrigliaUtils::$decodificaop[$regola['op']] == 'IN' || GrigliaUtils::$decodificaop[$regola['op']] == 'NOT IN') {
            $fieldparm = GrigliaUtils::$precaratterecampo[$regola['op']]
                    . str_replace(".", "", ":" . $regola['field'] . $key)
                    . GrigliaUtils::$postcaratterecampo[$regola['op']];
            $sqlparameter = str_replace(".", "", ":" . $regola['field'] . $key);
            $condition = GrigliaUtils::$precarattere[$regola['op']]
                    . $regola['field']
                    . GrigliaUtils::$postcarattere[$regola['op']]
                    . " " . GrigliaUtils::$decodificaop[$regola['op']] . " " . $fieldparm;
            $value = explode(",", str_replace(", ", ",", $regola['data']));
            //file_put_contents("/tmp/appo.log", "|" . $regola['op'] . "| " . $regola['data'] . "\n", FILE_APPEND);
            return array("condition" => $condition, "sqlparameter" => $sqlparameter, "value" => $value);
        }

        //file_put_contents("/tmp/appo.log", "|" . $regola['op'] . "| " . $regola['data'] . "\n", FILE_APPEND);
        $fieldparm = GrigliaUtils::$precaratterecampo[$regola['op']]
                . str_replace(".", "", ":" . $regola['field'] . $key)
                . GrigliaUtils::$postcaratterecampo[$regola['op']];
        $sqlparameter = str_replace(".", "", $regola['field'] . $key);
        $condition = GrigliaUtils::$precaratterecampo[$regola['op']]
                . $regola['field']
                . GrigliaUtils::$postcaratterecampo[$regola['op']] . " "
                . GrigliaUtils::$decodificaop[$regola['op']] . " " . $fieldparm;
        $value = GrigliaUtils::$precarattere[$regola['op']]
                . $regola['data']
                . GrigliaUtils::$postcarattere[$regola['op']];
        return array("condition" => $condition, "sqlparameter" => $sqlparameter, "value" => $value);
    }

    public static function setSingolaRegola($tipo, $regola)
    {
        //file_put_contents("/tmp/appo.log", "...". $tipo."-" .$regola['data']. "\n", FILE_APPEND);
        if (!$tipo || $tipo == "integer" || $tipo == "float") {
            GrigliaUtils::setVettoriPerNumero();
        }
        if ($tipo == 'date' || $tipo == 'datetime') {
            GrigliaUtils::setVettoriPerData();
            $regola['data'] = FiUtilita::data2db($regola['data']);
        } elseif ($tipo == 'string') {
            GrigliaUtils::setVettoriPerStringa();
            //file_put_contents("/tmp/appo.log", "..." . $tipo . "-" . $regola['data'] . "->" . strlen($regola['data']) . "\n", FILE_APPEND);
            $regola['data'] = preg_replace('/^(\'(.*)\'|"(.*)")$/', '$2$3', $regola['data']);
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
            $regola['data'] = "0";
        }
        if ($regola['data'] === 'true' || $regola['data'] === true) {
            GrigliaUtils::setVettoriPerBoolean();
            $regola['op'] = 'eq';
            $regola['data'] = "1";
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
        $container = $parametri['container'];

        $gestionepermessi = $container->get("ficorebundle.gestionepermessi");
        $operatorecorrente = $gestionepermessi->utentecorrente();

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

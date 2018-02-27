<?php

namespace Fi\CoreBundle\Utils;

use Fi\CoreBundle\Controller\FiUtilita;

class GrigliaDatiPrecondizioniUtils
{

    public static function setPrecondizioniAvanzate(&$q, &$primo, $parametri = array())
    {
        $doctrine = $parametri['doctrine'];
        $nometabella = $parametri['nometabella'];
        $entityName = $parametri['entityName'];
        $bundle = $parametri['bundle'];
        $precondizioniAvanzate = $parametri['precondizioniAvanzate'];
        $regole = array();

        foreach ($precondizioniAvanzate as $elem) {
            $nometabellaprecondizione = '';
            $nomecampoprecondizione = '';
            $valorecampoprecondizione = '';
            $operatoreprecondizione = '=';
            $operatorelogicoprecondizione = '';
            foreach ($elem as $keypre => $valuepre) {
                switch ($keypre) {
                    case 'nometabella':
                        $nometabellaprecondizione = $valuepre;
                        break;
                    case 'nomecampo':
                        $nomecampoprecondizione = $valuepre;
                        break;
                    case 'operatore':
                        $array_operatori = array('=' => 'eq', '<>' => 'ne', '<' => 'lt',
                            '<=' => 'le', '>' => 'gt', '>=' => 'ge',
                            'LIKE' => 'bw', 'NOT LIKE' => 'bn', 'IN' => 'in', 'NOT IN' => 'ni',
                            'LIKE' => 'eq', 'NOT LIKE' => 'en',
                            'LIKE' => 'cn', 'NOT LIKE' => 'nc', 'IS' => 'nu', 'IS NOT' => 'nn');
                        $operatoreprecondizione = $array_operatori[strtoupper($valuepre)];
                        break;
                    case 'valorecampo':
                        if (is_array($valuepre)) {
                            $type = $doctrine->getClassMetadata($parametri['entityName'])->getFieldMapping($nomecampoprecondizione);
                            $valorecampoprecondizione = self::elaboravalorecampo($type, $valuepre);
                        } else {
                            $valorecampoprecondizione = $valuepre;
                        }
                        break;
                    case 'operatorelogico':
                        $operatorelogicoprecondizione = strtoupper($valuepre);
                        break;
                    default:
                        break;
                }
            }

            $regole[] = array(
                'field' => sprintf("%s.%s", $nometabellaprecondizione, $nomecampoprecondizione),
                'op' => $operatoreprecondizione,
                'data' => $valorecampoprecondizione,
                'typof' => $operatorelogicoprecondizione);
        }
        $regolearray = array(
            'regole' => $regole,
            'doctrine' => $doctrine,
            'nometabella' => $nometabella,
            'entityName' => $entityName,
            'bundle' => $bundle
        );
        GrigliaRegoleUtils::setRegole($q, $primo, $regolearray);
    }

    private static function elaboravalorecampo($type, $valuepre)
    {
        $tipo = $type['type'];
        if ($tipo && ($tipo == 'date' || $tipo == 'datetime')) {
            GrigliaUtils::setVettoriPerData();
            foreach ($valuepre as $chiave => $valore) {
                $valuepre[$chiave] = FiUtilita::data2db($valore);
            }
            return implode(', ', $valuepre);
        } elseif ($tipo && $tipo == 'string') {
            GrigliaUtils::setVettoriPerStringa();
            foreach ($valuepre as $chiave => $valore) {
                $valuepre[$chiave] = strtolower($valore);
            }
            return implode(', ', $valuepre);
        } else {
            GrigliaUtils::setVettoriPerNumero();
            return implode(', ', $valuepre);
        }
    }

    public static function precondizioniToPrecondizioniAvanzate($parametri)
    {
        $precondizioni = GrigliaDatiUtils::getDatiPrecondizioni($parametri);

        $precondizioniAvanzate = GrigliaDatiUtils::getDatiPrecondizioniAvanzate($parametri);
        
        foreach ($precondizioni as $campoprecondizione => $valoreprecondizione) {
            if (strpos($campoprecondizione, ".") === false) {
                $nometabellaprecondizione = $parametri['nometabella'];
                $nomecampoprecondizione = $campoprecondizione;
            } else {
                $nometabellaprecondizione = substr($campoprecondizione, 0, strrpos($campoprecondizione, '.'));
                $nomecampoprecondizione = substr($campoprecondizione, strrpos($campoprecondizione, '.') + 1);
            }
            $precondizioniAvanzate[] = array('nometabella' => $nometabellaprecondizione,
                'nomecampo' => $nomecampoprecondizione,
                'operatore' => '=',
                'valorecampo' => $valoreprecondizione);
        }
        return $precondizioniAvanzate;
    }
}

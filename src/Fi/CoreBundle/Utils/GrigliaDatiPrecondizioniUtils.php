<?php

namespace Fi\CoreBundle\Utils;

use Fi\CoreBundle\Controller\FiUtilita;

class GrigliaDatiPrecondizioniUtils
{

    public static function setPrecondizioniAvanzate(&$q, $parametri = array())
    {
        $request = $parametri['request'];
        $precondizioniAvanzate = GrigliaDatiPrecondizioniUtils::precondizioniToPrecondizioniAvanzate($parametri);
        $doctrine = GrigliaParametriUtils::getDoctrineByEm($parametri);
        $bundle = $parametri['nomebundle'];
        $nometabella = $parametri['nometabella'];
        $entityName = $bundle . ':' . $nometabella;
        $regoleprecondizioni = array();
        $filtri = json_decode($request->get('filters'), true);
        /* dal filtro prende il tipo di operatore (AND o OR sono i due fin qui gestiti) */
        $tipof = $filtri['groupOp'];
        /* prende un vettore con tutte le ricerche */
        $regolegriglia = isset($filtri['rules']) ? $filtri['rules'] : array();
        foreach ($regolegriglia as $key => $regola) {
            $regolegriglia[$key]["typof"] = $tipof;
        }


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
                            $type = $doctrine->getClassMetadata($entityName)->getFieldMapping($nomecampoprecondizione);
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

            $regoleprecondizioni[] = array(
                'field' => sprintf("%s.%s", $nometabellaprecondizione, $nomecampoprecondizione),
                'op' => $operatoreprecondizione,
                'data' => $valorecampoprecondizione,
                'typof' => $operatorelogicoprecondizione);
        }
        /**/

        $regole = array(
            'regole' => array_merge($regolegriglia, $regoleprecondizioni),
            'doctrine' => $doctrine,
            'nometabella' => $nometabella,
            'entityName' => $entityName,
            'bundle' => $bundle
        );

        GrigliaRegoleUtils::setRegole($q, $regole);
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

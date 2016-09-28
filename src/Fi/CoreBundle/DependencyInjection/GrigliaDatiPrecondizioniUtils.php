<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaDatiPrecondizioniUtils
{
    public static function setPrecondizioni(&$q, &$primo, $parametri = array())
    {
        $precondizioni = $parametri['precondizioni'];

        $i = 1;
        foreach ($precondizioni as $nomecampopre => $precondizione) {
            if ($primo) {
                $q->where("$nomecampopre = :var$i");

                $primo = false;
            } else {
                $q->andWhere("$nomecampopre = :var$i");
            }
            $q->setParameter("var$i", $precondizione);
            ++$i;
        }
    }

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
                    $array_operatori = array('=' => 'eq', '<>' => 'ne', '<' => 'lt', '<=' => 'le', '>' => 'gt', '>=' => 'ge', 'LIKE' => 'bw', 'NOT LIKE' => 'bn', 'IN' => 'in', 'NOT IN' => 'ni', 'LIKE' => 'eq', 'NOT LIKE' => 'en', 'LIKE' => 'cn', 'NOT LIKE' => 'nc', 'IS' => 'nu', 'IS NOT' => 'nn'); //, '<>' => 'nt');
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

            $regole[] = array('field' => "$nometabellaprecondizione.$nomecampoprecondizione", 'op' => $operatoreprecondizione, 'data' => $valorecampoprecondizione);
            $tipof = $operatorelogicoprecondizione;

            GrigliaRegoleUtils::setRegole(
                $q, $primo, array(
                'regole' => $regole,
                'doctrine' => $doctrine,
                'nometabella' => $nometabella,
                'entityName' => $entityName,
                'bundle' => $bundle,
                'tipof' => $tipof,
                    )
            );
            $primo = false;
        }
    }

    private static function elaboravalorecampo($type, $valuepre)
    {
        $tipo = $type['type'];
        if ($tipo && ($tipo == 'date' || $tipo == 'datetime')) {
            GrigliaUtils::setVettoriPerData();
            foreach ($valuepre as $chiave => $valore) {
                $valuepre[$chiave] = fiUtilita::data2db($valore);
            }
        } elseif ($tipo && $tipo == 'string') {
            GrigliaUtils::setVettoriPerStringa();
            foreach ($valuepre as $chiave => $valore) {
                $valuepre[$chiave] = strtolower($valore);
            }
        } else {
            GrigliaUtils::setVettoriPerNumero();
        }
        /* se si tratta di valori numerici tutto ok, altrimenti non funziona */
        return implode(', ', $valuepre);
    }
}

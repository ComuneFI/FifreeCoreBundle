<?php

namespace Fi\CoreBundle\Controller;

/*
 * Insieme di funzioni utili
 * FiUtilita.
 *
 * @author Emidio Picariello
 */

use Fi\CoreBundle\DependencyInjection\PercentualiStringhe;

class FiUtilita
{



    public function percentualiConfrontoStringheVettore($parametri = array())
    {
        $percentuali = new PercentualiStringhe();

        return $percentuali->percentualiConfrontoStringheVettore($parametri);
    }

    public function percentualiConfrontoStringhe($parametri = array())
    {
        $percentuali = new PercentualiStringhe();

        return $percentuali->percentualiConfrontoStringhe($parametri);
    }

    public function sommaMinuti($parametri = array())
    {
//parametri obbligatori
        if (!isset($parametri['minuti'])) {
            return false;
        }
        $restotminuti = array();
        $resminuti = 0;
        $resore = 0;

        $minuti = $parametri['minuti'];

        $totminuti = array_sum($minuti);
        $resminuti = $totminuti % 60;
        $resore = ($totminuti - $resminuti) / 60;

        $restotminuti = array('ore' => $resore, 'minuti' => $resminuti);

        return $restotminuti;
    }

    /**
     * @param array  $parametri
     * @param string $parametri["tipo"]
     *
     * @return Array("segnouno"=>"xx", "segnodue"=>"yy") dove segnodue non obbligatorio
     */
    public function operatoreQuery($parametri = array())
    {
        $risposta = array();

        if (isset($parametri['tipo'])) {
            $tipocampo = $parametri['tipo'];
        } else {
            return array('segnouno' => '=');
        }

        switch ($tipocampo) {
            case 'date':
            case 'integer':
            case 'double':
                $operatore = '>=';
                $operatoredue = '<=';
                break;
            case 'string':
            case 'text':
                $operatore = 'LIKE';
                break;
            default:
                $operatore = '=';
                break;
        }

        $risposta['segnouno'] = $operatore;
        if (isset($operatoredue)) {
            $risposta['segnodue'] = $operatoredue;
        }

        return $risposta;
    }

    public static function data2db($giorno, $invertito = false, $senzalinea = false)
    {
        if ($giorno == '') {
            return null;
        }

        if (substr($giorno, 4, 1) == '-') {
            return $giorno;
        }

        $barragiorno = strpos($giorno, '/');
        $gg = substr($giorno, 0, $barragiorno);
        $restante = substr($giorno, $barragiorno + 1);

        $barra = strpos($restante, '/');
        $mm = substr($restante, 0, $barra);
        $aaaa = substr($restante, $barra + 1);

        $appogg = ($invertito ? $mm : $gg);
        $mm = ($invertito ? $gg : $mm);
        $gg = $appogg;

        $formattata = self::getDataFormattata($aaaa, $mm, $gg, $senzalinea);

        return $formattata;
    }

    private static function getDataFormattata($aaaa, $mm, $gg, $senzalinea)
    {
        $separatore = ($senzalinea ? '' : '-');

        $nuovadata = $aaaa . $separatore . $mm . $separatore . $gg;

        return strlen($gg) == 0 ? '' : $nuovadata;
    }

    public static function db2data($giorno, $senzalinea = false)
    {
        if (substr($giorno, 2, 1) == '/') {
            return $giorno;
        }

        if ($senzalinea) {
            $formattata = self::senzalinea($giorno);
        } else {
            $barra = strpos($giorno, '-');
            $aaaa = substr($giorno, 0, $barra);
            $restante = substr($giorno, $barra + 1);
            $barra = strpos($restante, '-');
            $mm = substr($restante, 0, $barra);
            $gg = substr($restante, $barra + 1);

            $formattata = (strlen($gg) == 0 ? '' : "$gg/$mm/$aaaa");
        }

        return $formattata;
    }

    private static function senzalinea($giorno)
    {
        $aaaa = substr($giorno, 0, 4);
        $mm = substr($giorno, 4, 2);
        $gg = substr($giorno, 6, 2);

        $formattata = (strlen($gg) == 0 ? '' : "$gg/$mm/$aaaa");

        return $formattata;
    }

    /**
     * @param array  $parametri
     * @param string $parametri["nomecodice"]      default = "codice"
     * @param string $parametri["nomedescrizione"] default = "descrizione"
     * @param array  $parametri["elementi"]        Array([0]=>("codice"=>1, "descrizione"=>"blaa"), [1]=>...)
     * @param string $parametri["selezionato"]     opzionale
     *
     * @return string
     */
    public function proSelect($parametri = array())
    {
        $stringaproselect = '';
        if (!isset($parametri['elementi'])) {
            return false;
        }

//parametri obbligatori
        $elementi = $parametri['elementi'];
        $attributi = $this->getProSelectAttribute($parametri);
        $selezionato = $attributi['selezionato'];
        $nomecodice = $attributi['nomecodice'];
        $nomedescrizione = $attributi['nomedescrizione'];

        foreach ($elementi as $elemento) {
            $elementonomecodice = $elemento[$nomecodice];
            $elementonomedescrizione = $elemento[$nomedescrizione];
            $elementoselezionato = ($elementonomecodice === $selezionato ? " selected='yes'" : '');
            $stringaproselect .= '<option value="' . $elementonomecodice . '"' . $elementoselezionato . '>' . $elementonomedescrizione . '</option>';
        }

        return $stringaproselect;
    }

    public function getProSelectAttribute($parametri)
    {
        $arrayritorno = array();
        $arrayritorno['selezionato'] = (isset($parametri['selezionato']) ? $parametri['selezionato'] : false);
        $arrayritorno['nomecodice'] = (isset($parametri['nomecodice']) ? $parametri['nomecodice'] : 'codice');
        $arrayritorno['nomedescrizione'] = (isset($parametri['nomedescrizione']) ? $parametri['nomedescrizione'] : 'descrizione');

        return $arrayritorno;
    }

    /**
     * @param $parametri["vettore"]
     * @param $parametri["chiave"]
     * @param $parametri["valore"]
     *
     * @return $vettorenuovo
     */
    public function cancellaDaVettore($parametri = array())
    {

//parametri obbligatori
        if (isset($parametri['vettore'])) {
            $vettore = $parametri['vettore'];
        } else {
            return false;
        }

//parametri obbligatori
        if (isset($parametri['chiave'])) {
            $chiave = $parametri['chiave'];
        } else {
            return $vettore;
        }

//parametri obbligatori
        if (isset($parametri['valore'])) {
            $valore = $parametri['valore'];
        } else {
            return $vettore;
        }

        $vettorenuovo = array();

        foreach ($vettore as $elemento) {
            if (!($elemento[$chiave] == $valore)) {
                $vettorenuovo[] = $elemento;
            }
        }

        return $vettorenuovo;
    }
}

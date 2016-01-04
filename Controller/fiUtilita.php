<?php

namespace Fi\CoreBundle\Controller;

/**
 * Insieme di funzioni utili
 * fiUtilita
 *
 * @author Emidio Picariello
 */
class fiUtilita {

  /**
   *
   * @param Array $parametri
   * @param string $parametri["elemento"] l'elemento da confrontare
   * @param Array $parameti["elenco"] l'elenco degli elementi con cui effettuare il confronto
   *
   * @return array
   */
  public function percentualiConfrontoStringheVettore($parametri = Array()) {

    $stringhepercento = Array();

    //parametri obbligatori
    if (isset($parametri["elemento"])) {
      $elemento = $parametri["elemento"];
    } else {
      return false;
    }

    //parametri obbligatori
    if (isset($parametri["elenco"])) {
      $elenco = $parametri["elenco"];
    } else {
      return false;
    }

    $rigarisposta = Array();
    $risposta = Array();


    foreach ($elenco as $elementoelenco) {
      $rigarisposta["elementoa"] = $elemento;
      $rigarisposta["elementob"] = $elementoelenco;
      $rigarisposta["percentuale"] = $this->percentualiConfrontoStringhe(array("stringaa" => $elemento, "stringab" => $elementoelenco));
      $risposta[] = $rigarisposta;
    }

    return $risposta;
  }

  /**
   *
   * confronta due stringhe e restiutisce la percentuale di somiglianza in base
   * alla posizione delle lettere uguali
   * se ci sono molte lettere uguali nella stessa posizione o in posizioni vicine
   * (con un parametro di tolleranza) allora la percentuale si alza
   *
   * @param Array $parametri
   * @param string $parametri["stringaa"] prima stringa da confrontare
   * @param string $parametri["stringab"] seconda stringa da confrontare
   * @param int $parametri["tolleranza"] numero di posizioni prima e dopo in cui cercare
   *
   * @return int
   */
  public function percentualiConfrontoStringhe($parametri = Array()) {

    //parametri obbligatori
    if (isset($parametri["stringaa"])) {
      $stringaa = $parametri["stringaa"];
    } else {
      return false;
    }

    //parametri obbligatori
    if (isset($parametri["stringab"])) {
      $stringab = $parametri["stringab"];
    } else {
      return false;
    }

    $tolleranzauno = (isset($parametri["tolleranza"]) ? $parametri["tolleranza"] : 1);
    $partecento = 0;

    $totalecento = strlen($stringaa) + strlen($stringab);

    for ($i = 0; $i < (strlen($stringab)); $i++) {

      $caratterea = (strlen($stringaa) >= $i ? substr($stringaa, $i, 1) : false);

      $posizioneinb = ($caratterea ? strpos(strtolower($stringab), strtolower($caratterea), (($i - $tolleranzauno) >= 0 ? ($i - $tolleranzauno) : 0)) : false);

      if (!($posizioneinb === false)) {

        if ($posizioneinb == $i) {
          $partecento += 2;
        } elseif ((($i + $tolleranzauno) >= $posizioneinb) and ( ($i - $tolleranzauno) <= $posizioneinb)) {
          $partecento += 1;
        }
      }
    }

    return ($partecento * 100 / $totalecento);
  }

  /**
   *
   * @param Array $parametri
   * @param Array(x,x,x,x) $parametri["minuti"]
   * @return int
   */
  public function sommaMinuti($parametri = Array()) {

    $restotminuti = Array();
    $resminuti = 0;
    $resore = 0;

    //parametri obbligatori
    if (isset($parametri["minuti"])) {
      $minuti = $parametri["minuti"];
    } else {
      return false;
    }


    $totminuti = array_sum($minuti);
    $resminuti = $totminuti % 60;
    $resore = ($totminuti - $resminuti) / 60;

    $restotminuti = Array("ore" => $resore, "minuti" => $resminuti);

    return $restotminuti;
  }

  /**
   *
   * @param Array $parametri
   * @param string $parametri["tipo"]
   *
   * @return Array("segnouno"=>"xx", "segnodue"=>"yy") dove segnodue non obbligatorio
   */
  public function operatoreQuery($parametri = Array()) {
    $risposta = Array();


    if (isset($parametri["tipo"]))
      $tipocampo = $parametri["tipo"];
    else
      return Array("segnouno" => "=");


    switch ($tipocampo) {
      case "date":
      case "integer":
      case "double":
        $operatore = ">=";
        $operatoredue = "<=";
        break;
      case "string":
      case "text":
        $operatore = "LIKE";
        break;
      default:
        $operatore = "=";
        break;
    }

    $risposta["segnouno"] = $operatore;
    if (isset($operatoredue))
      $risposta["segnodue"] = $operatoredue;

    return $risposta;
  }

  static public function data2db($giorno, $invertito = false) {

    if ($giorno == "") {
      return NULL;
    }

    if (substr($giorno, 4, 1) == "-") {
      return $giorno;
    }


    $barra = strpos($giorno, "/");
    $gg = substr($giorno, 0, $barra);
    $restante = substr($giorno, $barra + 1);
    $barra = strpos($restante, "/");
    $mm = substr($restante, 0, $barra);
    $aaaa = substr($restante, $barra + 1);

    $appogg = ($invertito ? $mm : $gg);
    $mm = ($invertito ? $gg : $mm);
    $gg = $appogg;

    $formattata = (strlen($gg) == 0 ? "" : "$aaaa-$mm-$gg");

    return $formattata;
  }

  static public function db2data($giorno, $senzalinea = false) {

    if (substr($giorno, 2, 1) == "/") {

      return $giorno;
    }

    if ($senzalinea) {
      $formattata = fiUtilita::senzalinea($giorno);
    } else {

      $barra = strpos($giorno, "-");
      $aaaa = substr($giorno, 0, $barra);
      $restante = substr($giorno, $barra + 1);
      $barra = strpos($restante, "-");
      $mm = substr($restante, 0, $barra);
      $gg = substr($restante, $barra + 1);

      $formattata = (strlen($gg) == 0 ? "" : "$gg/$mm/$aaaa");
    }
    return $formattata;
  }

  static private function senzalinea($giorno) {
    $aaaa = substr($giorno, 0, 4);
    $mm = substr($giorno, 4, 2);
    $gg = substr($giorno, 6, 2);

    $formattata = (strlen($gg) == 0 ? "" : "$gg/$mm/$aaaa");

    return $formattata;
  }

  /**
   *
   * @param Array $parametri
   * @param string $parametri["nomecodice"] default = "codice"
   * @param string $parametri["nomedescrizione"] default = "descrizione"
   * @param Array $parametri["elementi"] Array([0]=>("codice"=>1, "descrizione"=>"blaa"), [1]=>...)
   * @param string $parametri["selezionato"] opzionale
   * @return string 
   */
  function proSelect($parametri = Array()) {
    $stringaproselect = '';

    //parametri obbligatori
    if (isset($parametri["elementi"])) {
      $elementi = $parametri["elementi"];
    } else {
      return false;
    }

    $selezionato = (isset($parametri["selezionato"]) ? $parametri["selezionato"] : false);
    $nomecodice = (isset($parametri["nomecodice"]) ? $parametri["nomecodice"] : "codice");
    $nomedescrizione = (isset($parametri["nomedescrizione"]) ? $parametri["nomedescrizione"] : "descrizione");

    foreach ($elementi as $elemento) {
      $stringaproselect .= "<option value=" . $elemento[$nomecodice] . "" . ($elemento[$nomecodice] === $selezionato ? " selected='yes'" : "") . ">" . $elemento[$nomedescrizione] . "</option>";
    }

    return $stringaproselect;
  }

  /**
   * @param $parametri["vettore"]
   * @param $parametri["chiave"]
   * @param $parametri["valore"]
   *
   * @return $vettorenuovo
   */
  function cancellaDaVettore($parametri = Array()) {


    //parametri obbligatori
    if (isset($parametri["vettore"])) {
      $vettore = $parametri["vettore"];
    } else {
      return false;
    }

    //parametri obbligatori
    if (isset($parametri["chiave"])) {
      $chiave = $parametri["chiave"];
    } else {
      return $vettore;
    }

    //parametri obbligatori
    if (isset($parametri["valore"])) {
      $valore = $parametri["valore"];
    } else {
      return $vettore;
    }


    $vettorenuovo = Array();

    foreach ($vettore as $elemento) {
      if (!($elemento[$chiave] == $valore)) {
        $vettorenuovo[] = $elemento;
      }
    }

    return $vettorenuovo;
  }

  public function creaBottone() {
    
  }

  public function array_searchRecursive($needle, $haystack) {
    foreach ($haystack as $key => $val) {
      if (stripos(implode("", $val), $needle) > 0) {
        return $key;
      }
    }
    return false;
  }

}

?>

<?php

namespace Fi\CoreBundle\DependencyInjection;

class PercentualiStringhe
{

    /**
     * @param array  $parametri
     * <pre>string $parametri['elemento'] l'elemento da confrontare</pre>
     * <pre>array  $parametri['elenco']    l'elenco degli elementi con cui effettuare il confronto</pre>
     *
     * @return array
     */
    public function percentualiConfrontoStringheVettore($parametri = array())
    {
        //parametri obbligatori
        if (!isset($parametri['elemento']) || !isset($parametri['elenco']) || empty($parametri['elenco'])) {
            return false;
        }

        $elemento = $parametri['elemento'];
        $elenco = $parametri['elenco'];

        $rigarisposta = array();
        $risposta = array();

        foreach ($elenco as $elementoelenco) {
            $rigarisposta['elementoa'] = $elemento;
            $rigarisposta['elementob'] = $elementoelenco;
            $rigarisposta['percentuale'] = $this->percentualiConfrontoStringhe(array('stringaa' => $elemento, 'stringab' => $elementoelenco));
            $risposta[] = $rigarisposta;
        }

        return $risposta;
    }

    /**
     * confronta due stringhe e restiutisce la percentuale di somiglianza in base
     * alla posizione delle lettere uguali
     * se ci sono molte lettere uguali nella stessa posizione o in posizioni vicine
     * (con un parametro di tolleranza) allora la percentuale si alza.
     *
     * @param array  $parametri
     * @param string $parametri["stringaa"]   prima stringa da confrontare
     * @param string $parametri["stringab"]   seconda stringa da confrontare
     * @param int    $parametri["tolleranza"] numero di posizioni prima e dopo in cui cercare
     *
     * @return int
     */
    public function percentualiConfrontoStringhe($parametri = array())
    {
        //parametri obbligatori
        if (!isset($parametri['stringaa']) || !isset($parametri['stringab'])) {
            return false;
        }
        $stringaa = $parametri['stringaa'];
        $stringab = $parametri['stringab'];

        $tolleranzauno = (isset($parametri['tolleranza']) ? $parametri['tolleranza'] : 1);

        $partecento = 0;
        $strlensa = strlen($stringaa);
        $strlensb = strlen($stringab);
        $totalecento = $strlensa + $strlensb;

        for ($i = 0; $i < $strlensb; ++$i) {
            $caratterea = $this->getCarattereAPercentualeConfrontoStringhe($i, $stringaa, $strlensa);
            $offset = $this->getOffsetPercentualeConfrontoStringhe($i, $tolleranzauno);
            $strpos = strpos(strtolower($stringab), strtolower($caratterea), $offset);
            $posizioneinb = $caratterea ? $strpos : false;

            $partecento = $this->partecento($i, $posizioneinb, $tolleranzauno, $partecento);
        }
        $perc = (($partecento * 100) / $totalecento);

        return $perc;
    }

    private function getCarattereAPercentualeConfrontoStringhe($i, $stringaa, $strlensa)
    {
        return $strlensa >= $i ? substr($stringaa, $i, 1) : false;
    }

    private function getOffsetPercentualeConfrontoStringhe($i, $tolleranzauno)
    {
        return (($i - $tolleranzauno) >= 0) ? ($i - $tolleranzauno) : 0;
    }

    private function partecento($i, $posizioneinb, $tolleranzauno, $partecento)
    {
        if (!($posizioneinb === false)) {
            if ($posizioneinb == $i) {
                $partecento += 2;
            } elseif (($i + $tolleranzauno >= $posizioneinb) and ( $i - $tolleranzauno <= $posizioneinb)) {
                $partecento += 1;
            }
        }

        return $partecento;
    }
    /* Fatta da Emidio, sopra Andrea l'ha semplificata ma non sapendo la "tolleranza" lascio il backup qui
      public function percentualiConfrontoStringhe($parametri = array())
      {

      //parametri obbligatori
      if (isset($parametri['stringaa'])) {
      $stringaa = $parametri['stringaa'];
      } else {
      return false;
      }

      //parametri obbligatori
      if (isset($parametri['stringab'])) {
      $stringab = $parametri['stringab'];
      } else {
      return false;
      }

      $tolleranzauno = (isset($parametri['tolleranza']) ? $parametri['tolleranza'] : 1);
      $partecento = 0;

      $totalecento = strlen($stringaa) + strlen($stringab);

      for ($i = 0; $i < (strlen($stringab)); ++$i) {
      $caratterea = (strlen($stringaa) >= $i ? substr($stringaa, $i, 1) : false);

      $posizioneinb =
      ($caratterea ? strpos(strtolower($stringab), strtolower($caratterea),
      (($i - $tolleranzauno) >= 0 ? ($i - $tolleranzauno) : 0)) : false);

      if (!($posizioneinb === false)) {
      if ($posizioneinb == $i) {
      $partecento += 2;
      } elseif ((($i + $tolleranzauno) >= $posizioneinb) and (($i - $tolleranzauno) <= $posizioneinb)) {
      $partecento += 1;
      }
      }
      }

      return $partecento * 100 / $totalecento;
      } */

    /*
     * @param array          $parametri
     * @param Array(x,x,x,x) $parametri["minuti"]
     *
     * @return int
     */
}

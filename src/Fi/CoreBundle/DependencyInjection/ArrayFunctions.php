<?php

namespace Fi\CoreBundle\DependencyInjection;

class ArrayFunctions
{

    /**
     * La funzione cerca un valore $elem nell'array multidimensionale $array all'interno di ogni elemento con chiave $key di ogni riga di array
     * e restituisce l'indice.
     *
     * @param $elem mixed Elemento da cercare nell'array
     * @param $array Array nel quale cercare
     * @param $key string Nome della chiave nella quale cercare $elem
     *
     * @return mixed False se non trovato l'elemento, altrimenti l'indice in cui si è trovato il valore
     */
    public static function inMultiarray($elem, $array, $key)
    {
        foreach ($array as $indice => $value) {
            if (!is_array($value)) {
                return false;
            }
            if (array_key_exists($key, $value)) {
                foreach ($value as $nomecolonna => $colonna) {
                    if ($colonna === $elem && $nomecolonna == $key) {
                        return $indice;
                    }
                }
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * La funzione cerca un valore $elem nell'array multidimensionale $array all'interno di ogni elemento con chiave $key di ogni riga di array
     * e restituisce l'indice
     *
     * @param $elem mixed Elemento da cercare nell'array
     * @param $array Array nel quale cercare
     * @param $key mixed Nome della chiave nella quale cercare $elem
     * @return Mixed False se non trovato l'elemento, altrimenti il vettore con tutti gli indici
     */
    public static function inMultiarrayTutti($elem, $array, $key)
    {

        $trovato = array();

        foreach ($array as $indice => $value) {
            if (!is_array($value)) {
                return false;
            }
            if (array_key_exists($key, $value)) {
                foreach ($value as $nomecolonna => $colonna) {
                    if ($colonna === $elem && $nomecolonna == $key) {
                        $trovato[] = $indice;
                    }
                }
            } else {
                return false;
            }
        }
        return (count($trovato) > 0 ? $trovato : false);
    }

    /**
     * La funzione cerca un valore $elem nell'array multidimensionale $array all'interno di ogni elemento con chiave $key di ogni riga di array
     * e restituisce l'indice
     *
     * @param $array Array nel quale cercare
     * @param $search array Chiave-valore da cercare
     * @return Mixed False se non trovato l'elemento, altrimenti l'indice in cui si trova il valore
     */
    public static function multiInMultiarray($array, $search, $debug = false, $tutti = false)
    {
        $primo = true;
        $vettorerisultati = array();

        foreach ($search as $key => $singolaricerca) {
            $trovato = self::inMultiarrayTutti($singolaricerca, $array, $key, $debug);

            if ($trovato === false) {
                $vettorerisultati = false;
                break;
            }

            if ($primo) {
                $vettorerisultati = $trovato;
            } else {
                $vettorerisultati = array_intersect($vettorerisultati, $trovato);
            }

            $primo = false;
        }

        if ($vettorerisultati === false) {
            $risposta = false;
        } elseif ($tutti === false) {
            $risposta = reset($vettorerisultati);
        } else {
            $risposta = $vettorerisultati;
        }

        return $risposta;
    }

    /**
     * La funzione ordina un array multidimensionale $array.
     *
     * @param $array Array da ordinare
     * @param $key string Nome della chiave dell'array per cui ordinare
     * @param $type int Tipo di ordinamento SORT_ASC, SORT_DESC
     *
     * @return array Ritorna l'array ordinato
     *
     * @example arrayOrderby($rubrica,"cognome",SORT_ASC);<br/>$rubrica = array();<br/>$rubrica[] = array("matricola" => 99999, "cognome" => "rossi", "nome" => "mario");<br/>$rubrica[] = array("matricola" => 99998, "cognome" => "bianchi", "nome" => "andrea");<br/>$rubrica[] = array("matricola" => 99997, "cognome" => "verdi", "nome" => "michele");<br/>rusulterà<br/>$rubrica[0]("matricola"=>99998,"cognome"=>"bianchi","nome"=>"andrea")<br/>$rubrica[1]("matricola"=>99999,"cognome"=>"rossi","nome"=>"mario")<br/>$rubrica[2]("matricola"=>99997,"cognome"=>"verdi","nome"=>"michele")<br/>
     */
    public static function arrayOrderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row) {
                    $tmp[$key] = $row[$field];
                }
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    public function arraySearchRecursive($needle, $haystack)
    {
        foreach ($haystack as $key => $val) {
            if (stripos(implode('', $val), $needle) > 0) {
                return $key;
            }
        }

        return false;
    }
}

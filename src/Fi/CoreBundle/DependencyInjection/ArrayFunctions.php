<?php

namespace Fi\CoreBundle\DependencyInjection;

class ArrayFunctions
{

    /**
     * La funzione cerca un valore $elem nell'array multidimensionale $array all'interno di ogni elemento con chiave $key di ogni riga di array
     * e restituisce l'indice.
     *
     * @param $elem Oggetto da cercare
     * @param $array Array nel quale cercare
     * @param $key Nome della chiave nella quale cercare $elem
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
     * @param $elem Oggetto da cercare
     * @param $array Array nel quale cercare
     * @param $key Nome della chiave nella quale cercare $elem
     * @return Mixed False se non trovato l'elemento, altrimenti il vettore con tutti gli indici 
     */
    static function inMultiarrayTutti($elem, $array, $key, $debug)
    {

        if ($debug) {
            var_dump($elem);

            var_dump($key);
        }


        $trovato = array();

        foreach ($array as $indice => $value) {
            if (!is_array($value)) {
                return false;
            }
            if (array_key_exists($key, $value)) {
                foreach ($value as $nomecolonna => $colonna) {
                    if ($colonna === $elem && $nomecolonna == $key) {
                        if ($debug) {
                            echo "$colonna Ã¨ uguale a $elem a indice $indice \n";
                            echo "$nomecolonna Ã¨ uguale a $key a indice $indice \n";
                            var_dump($array[$indice]);
                            echo "\n";
                        }

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
     * @param $search Chiave-valore da cercare
     * @return Mixed False se non trovato l'elemento, altrimenti l'indice in cui si Ã¨ trovato il valore
     */
    static function multiInMultiarray($array, $search, $debug = false, $tutti = false)
    {
        $primo = true;
        $vettorerisultati = array();

        if ($debug) {

            echo "<br>\n vettore search <br>\n";
            var_dump($search);
            echo "<br>\n fine vettore search  <br>\n";

            echo "<br>\n vettorecompleto <br>\n";
            var_dump($array);
            echo "<br>\n fine vettorecompleto <br>\n";
        }


        foreach ($search as $key => $singolaricerca) {
            $trovato = self::inMultiarrayTutti($singolaricerca, $array, $key, $debug);

            if ($debug) {
                echo $primo ? "<br>\n primo <br>\n" : "<br>\n non primo <br>\n";
                var_dump($trovato);
                echo $primo ? "<br>\n fine primo <br>\n" : "<br>\n fine non primo <br>\n";
            }

            if ($trovato === false) {
                break;
            }

            if ($primo) {
                $vettorerisultati = $trovato;
            } else {
                $vettorerisultati = array_intersect($vettorerisultati, $trovato);
                if ($debug) {
                    echo "<br>\n vettorerisultati<br>\n";
                    var_dump($vettorerisultati);
                    echo "<br>\n fine vettorerisultati<br>\n";
                }
            }

            $primo = false;
        }

        if ($vettorerisultati === false) {
            $risposta = false;
        } else if ($tutti === false) {
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
     * @param $key Nome della chiave dell'array per cui ordinare
     * @param $type Tipo di ordinamento SORT_ASC, SORT_DESC
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
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
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

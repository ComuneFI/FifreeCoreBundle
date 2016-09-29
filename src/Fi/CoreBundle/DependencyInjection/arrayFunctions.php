<?php

namespace Fi\CoreBundle\DependencyInjection;

class arrayFunctions
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
    public static function in_multiarray($elem, $array, $key)
    {
        foreach ($array as $indice => $value) {
            if (!is_array($value)) {
                return false;
            }
            if (array_key_exists($key, $value)) {
                foreach ($value as $colonna) {
                    if ($colonna === $elem) {
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
     * La funzione ordina un array multidimensionale $array.
     *
     * @param $array Array da ordinare
     * @param $key Nome della chiave dell'array per cui ordinare
     * @param $type Tipo di ordinamento SORT_ASC, SORT_DESC
     *
     * @return array Ritorna l'array ordinato
     *
     * @example array_orderby($rubrica,"cognome",SORT_ASC);<br/>$rubrica = array();<br/>$rubrica[] = array("matricola" => 99999, "cognome" => "rossi", "nome" => "mario");<br/>$rubrica[] = array("matricola" => 99998, "cognome" => "bianchi", "nome" => "andrea");<br/>$rubrica[] = array("matricola" => 99997, "cognome" => "verdi", "nome" => "michele");<br/>rusulterà<br/>$rubrica[0]("matricola"=>99998,"cognome"=>"bianchi","nome"=>"andrea")<br/>$rubrica[1]("matricola"=>99999,"cognome"=>"rossi","nome"=>"mario")<br/>$rubrica[2]("matricola"=>99997,"cognome"=>"verdi","nome"=>"michele")<br/>
     */
    public static function array_orderby()
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

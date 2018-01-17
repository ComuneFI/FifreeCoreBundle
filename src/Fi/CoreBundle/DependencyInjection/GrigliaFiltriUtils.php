<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaFiltriUtils
{
    /**
     * Funzione alla quale si passano i filtri nel formato gestito da jqGrid e
     * che restituisce una stringa che contiene la descrizione in linguaggio
     * naturale.
     *
     * @param array   $parametri
     *
     * @return string
     */
    public static function traduciFiltri($parametri = array())
    {
        $genericofiltri = $parametri['filtri'];

        $filtri = isset($genericofiltri->rules) ? $genericofiltri->rules : '';
        $tipofiltro = isset($genericofiltri->groupOp) ? $genericofiltri->groupOp : '';
        GrigliaUtils::$decodificaop = array(
            'eq' => ' è uguale a ',
            'ne' => ' è diverso da ',
            'lt' => ' è inferiore a ',
            'le' => ' è inferiore o uguale a ',
            'gt' => ' è maggiore di ',
            'ge' => ' è maggiore o uguale di ',
            'bw' => ' comincia con ',
            'bn' => ' non comincia con ',
            'in' => ' è uno fra ',
            'ni' => ' non è uno fra ',
            'ew' => ' finisce con ',
            'en' => ' con finisce con ',
            'cn' => ' contiene ',
            'nc' => ' non contiene ',
            'nu' => ' è vuoto',
            'nn' => ' non è vuoto', );

        if (!isset($filtri) or (!$filtri)) {
            return '';
        }

        $filtrodescritto = self::getFiltrodescritto($filtri, $tipofiltro);

        return $filtrodescritto;
    }

    public static function getFiltrodescritto($filtri, $tipofiltro)
    {
        $filtrodescritto = ('I dati mostrati rispondono a'.($tipofiltro == 'AND' ? ' tutti i' : 'd almeno uno dei').' seguenti criteri: ');

        foreach ($filtri as $indice => $filtro) {
            $campo = $filtro->field;
            $operatore = $filtro->op;
            $data = $filtro->data;
            $filtrodescritto .= ($indice !== 0 ? ($tipofiltro == 'AND' ? ' e ' : ' o ') : '').
                    GrigliaUtils::toCamelCase(array('str' => $campo, 'primamaiuscola' => true)).
                    GrigliaUtils::$decodificaop[$operatore]."\"$data\"";
        }

        $filtrodescritto .= '.';

        return $filtrodescritto;
    }
}

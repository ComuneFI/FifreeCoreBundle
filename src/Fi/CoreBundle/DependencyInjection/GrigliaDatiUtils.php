<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaDatiUtils
{

    public static function getTabellejNormalizzate($parametri) 
    {
        $tabellej = (isset($parametri['tabellej']) ? $parametri['tabellej'] : null);
        if (is_object($tabellej)) {
            $tabellej = get_object_vars($tabellej);
        }
        return $tabellej;
    }

    public static function setTabelleJoin(&$q, $parametri = array()) 
    {
        $tabellej = $parametri['tabellej'];
        $nometabella = $parametri['nometabella'];

        foreach ($tabellej as $tabellaj) {
            if (is_object($tabellaj)) {
                $tabellaj = get_object_vars($tabellaj);
            }
            /* Serve per far venire nella getArrayResult() anche i campi della tabella il leftjoin
              altrimenti mostra solo quelli della tabella con alias a */
            $q->addSelect(array($tabellaj['tabella']));
            $q = $q->leftJoin((isset($tabellaj['padre']) ? $tabellaj['padre'] : $nometabella) . '.' . $tabellaj['tabella'], $tabellaj['tabella']);
        }
    }

    public static function getDatiDecodifiche($parametri) 
    {
        return (isset($parametri['decodifiche']) ? $parametri['decodifiche'] : null);
    }

    public static function getDatiEscludere($parametri) 
    {
        return (isset($parametri['escludere']) ? $parametri['escludere'] : null);
    }

    public static function getDatiNospan($parametri) 
    {
        return (isset($parametri['nospan']) ? $parametri['nospan'] : false);
    }

    public static function getDatiPrecondizioni($parametri) 
    {
        return (isset($parametri['precondizioni']) ? $parametri['precondizioni'] : false);
    }

    public static function getDatiPrecondizioniAvanzate($parametri) 
    {
        return (isset($parametri['precondizioniAvanzate']) ? $parametri['precondizioniAvanzate'] : false);
    }

    public static function getDatiCampiExtra($parametri) 
    {
        return (isset($parametri['campiextra']) ? $parametri['campiextra'] : null);
    }

    public static function getDatiOrdineColonne($parametri) 
    {
        $ordinecolonne = (isset($parametri['ordinecolonne']) ? $parametri['ordinecolonne'] : null);
        if (!isset($ordinecolonne)) {
            $ordinecolonne = GrigliaUtils::ordinecolonne($parametri);
        }

        return $ordinecolonne;
    }

    public static function getDatiOrdinamento(&$sidx, $nometabella) 
    {
        /* se non è passato nessun campo (ipotesi peregrina) usa id */
        if (!$sidx) {
            $sidx = $nometabella . '.id';
        } elseif (strrpos($sidx, '.') == 0) {
            if (strrpos($sidx, ',') == 0) {
                $sidx = $nometabella . '.' . $sidx; // un solo campo
            } else { // più campi, passati separati da virgole
                $parti = explode(',', $sidx);
                $sidx = '';
                foreach ($parti as $parte) {
                    if (trim($sidx) != '') {
                        $sidx = $sidx . ',';
                    }
                    $sidx = $sidx . $nometabella . '.' . trim($parte);
                }
            }
        }
    }

    public static function valorizzaVettore(&$vettoreriga, $parametri) 
    {
        $tabella = $parametri['tabella'];
        $nomecampo = $parametri['nomecampo'];
        $doctrine = $parametri['doctrine'];
        $ordinecampo = $parametri['ordinecampo'];
        $decodifiche = $parametri['decodifiche'];

        $vettoreparcampi = $doctrine->getMetadataFactory()->getMetadataFor($tabella)->fieldMappings;

        if (is_object($vettoreparcampi)) {
            $vettoreparcampi = get_object_vars($vettoreparcampi);
        }

        $singolocampo = $parametri['singolocampo'];

        if (isset($decodifiche[$nomecampo])) {
            $vettoreriga[] = $decodifiche[$nomecampo][$singolocampo];
        } else {
            if (isset($vettoreparcampi[$nomecampo]['type']) && ($vettoreparcampi[$nomecampo]['type'] == 'date' || $vettoreparcampi[$nomecampo]['type'] == 'datetime') && $singolocampo) {
                if (isset($ordinecampo)) {
                    $vettoreriga[$ordinecampo] = $singolocampo->format('d/m/Y');
                } else {
                    $vettoreriga[] = $singolocampo->format('d/m/Y');
                }
            } elseif (isset($vettoreparcampi[$nomecampo]['type']) && ($vettoreparcampi[$nomecampo]['type'] == 'time') && $singolocampo) {
                if (isset($ordinecampo)) {
                    $vettoreriga[$ordinecampo] = $singolocampo->format('H:i');
                } else {
                    $vettoreriga[] = $singolocampo->format('H:i');
                }
            } else {
                if (isset($ordinecampo)) {
                    $vettoreriga[$ordinecampo] = $singolocampo;
                } else {
                    $vettoreriga[] = $singolocampo;
                }
            }
        }
    }

    public static function campoElencato($parametriCampoElencato) 
    {
        $tabellej = $parametriCampoElencato['tabellej'];
        $nomecampo = $parametriCampoElencato['nomecampo'];
        $campoelencato = $parametriCampoElencato['campoelencato'];
        $vettoreriga = $parametriCampoElencato['vettoreriga'];
        $singolo = $parametriCampoElencato['singolo'];
        $doctrine = $parametriCampoElencato['doctrine'];
        $bundle = $parametriCampoElencato['bundle'];
        $decodifiche = $parametriCampoElencato['decodifiche'];

        if (isset($parametriCampoElencato['ordinecampo'])) {
            $ordinecampo = $parametriCampoElencato['ordinecampo'];
        } else {
            $ordinecampo = null;
        }

        if (isset($tabellej[$campoelencato])) {
            foreach ($tabellej[$campoelencato]['campi'] as $campoelencatointerno) {
                $parametriCampoElencatoInterno['tabellej'] = $tabellej;
                $parametriCampoElencatoInterno['nomecampo'] = $campoelencato;
                $parametriCampoElencatoInterno['campoelencato'] = $campoelencatointerno;
                $parametriCampoElencatoInterno['vettoreriga'] = $vettoreriga;
                $parametriCampoElencatoInterno['singolo'] = $singolo;
                $parametriCampoElencatoInterno['doctrine'] = $doctrine;
                $parametriCampoElencatoInterno['bundle'] = $bundle;
                $parametriCampoElencatoInterno['ordinecampo'] = $ordinecampo;
                $parametriCampoElencatoInterno['decodifiche'] = $decodifiche;

                $vettoreriga = self::campoElencato($parametriCampoElencatoInterno);
            }
        } else {
            if (isset($tabellej[$nomecampo]['padre'])) {
                $fields = $singolo[$tabellej[$nomecampo]['padre']][$tabellej[$nomecampo]['tabella']] ? $singolo[$tabellej[$nomecampo]['padre']][$tabellej[$nomecampo]['tabella']][$campoelencato] : '';
            } else {
                $fields = $singolo[$tabellej[$nomecampo]['tabella']] ? $singolo[$tabellej[$nomecampo]['tabella']][$campoelencato] : '';
            }
            self::valorizzaVettore($vettoreriga, array('singolocampo' => $fields, 'tabella' => $bundle . ':' . $tabellej[$nomecampo]['tabella'], 'nomecampo' => $campoelencato, 'doctrine' => $doctrine, 'ordinecampo' => $ordinecampo, 'decodifiche' => $decodifiche));
        }

        return $vettoreriga;
    }

}

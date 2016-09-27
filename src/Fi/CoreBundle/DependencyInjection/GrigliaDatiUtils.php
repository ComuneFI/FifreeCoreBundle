<?php

namespace Fi\CoreBundle\DependencyInjection;

class GrigliaDatiUtils {

    public static function getTabellejNormalizzate($parametri) {
        $tabellej = (isset($parametri['tabellej']) ? $parametri['tabellej'] : null);
        if (is_object($tabellej)) {
            $tabellej = get_object_vars($tabellej);
        }
        return $tabellej;
    }

    public static function setTabelleJoin(&$q, $parametri = array()) {
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

    public static function getDatiDecodifiche($parametri) {
        return (isset($parametri['decodifiche']) ? $parametri['decodifiche'] : null);
    }

    public static function getDatiEscludere($parametri) {
        return (isset($parametri['escludere']) ? $parametri['escludere'] : null);
    }

    public static function getDatiNospan($parametri) {
        return (isset($parametri['nospan']) ? $parametri['nospan'] : false);
    }

    public static function getDatiPrecondizioni($parametri) {
        return (isset($parametri['precondizioni']) ? $parametri['precondizioni'] : false);
    }

    public static function getDatiPrecondizioniAvanzate($parametri) {
        return (isset($parametri['precondizioniAvanzate']) ? $parametri['precondizioniAvanzate'] : false);
    }

    public static function getDatiCampiExtra($parametri) {
        return (isset($parametri['campiextra']) ? $parametri['campiextra'] : null);
    }

    public static function getDatiOrdineColonne($parametri) {
        $ordinecolonne = (isset($parametri['ordinecolonne']) ? $parametri['ordinecolonne'] : null);
        if (!isset($ordinecolonne)) {
            $ordinecolonne = GrigliaUtils::ordinecolonne($parametri);
        }

        return $ordinecolonne;
    }

    public static function getDatiOrdinamento(&$sidx, $nometabella) {
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

    public static function setPrecondizioni(&$q, &$primo, $parametri = array()) {
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

}

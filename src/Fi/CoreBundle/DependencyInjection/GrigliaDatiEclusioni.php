<?php

namespace Fi\CoreBundle\DependencyInjection;

use Fi\CoreBundle\Controller\GestionepermessiController;
use Fi\CoreBundle\DependencyInjection\GrigliaUtils;

class GrigliaDatiEclusioni
{

    public static function getDatiEscludereDaTabella($parametri)
    {
        if (!isset($parametri["nometabella"])) {
            return false;
        }

        if ((isset($parametri["output"])) && ($parametri["output"] == "stampa")) {
            $output = "stampa";
        } else {
            $output = "index";
        }

        $nometabella = $parametri["nometabella"];

        $doctrine = $parametri['container']->get('doctrine');

        //$bundle = $parametri["nomebundle"];
        //Fisso il CoreBundle perchè si passa sempre da questo bundle per le esclusioni
        $bundle = "FiCoreBundle";

        $gestionepermessi = new GestionepermessiController($parametri["container"]);
        $operatorecorrente = $gestionepermessi->utentecorrenteAction();

        $escludi = array();

        $q = $doctrine->getRepository($bundle . ":tabelle")->findBy(array("operatori_id" => $operatorecorrente["id"], "nometabella" => $nometabella));

        if (!$q) {
            unset($q);
            $q = $doctrine->getRepository($bundle . ":tabelle")->findBy(array("operatori_id" => null, "nometabella" => $nometabella));
        }

        if ($q) {
            foreach ($q as $riga) {
                $escludi[] = GrigliaUtils::getCampiEsclusi($riga, $output);
            }
        }

        return $escludi;
    }

    public static function getDatiEscludere($parametri)
    {
        return isset($parametri['escludere']) ? $parametri['escludere'] : null;
    }

}

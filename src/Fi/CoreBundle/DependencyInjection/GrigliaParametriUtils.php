<?php

namespace Fi\CoreBundle\DependencyInjection;

use Fi\CoreBundle\Controller\GestionepermessiController;
use Fi\CoreBundle\DependencyInjection\GrigliaUtils;

class GrigliaParametriUtils
{

    public static function getAliasTestataPerGriglia($paricevuti)
    {
        $alias = isset($paricevuti['dettaglij']) ? $paricevuti['dettaglij'] : array();

        if (is_object($alias)) {
            $alias = get_object_vars($alias);
        }

        return $alias;
    }

    public static function getOrdineColonneTestataPerGriglia($paricevuti)
    {
        $ordinecolonne = isset($paricevuti['ordinecolonne']) ? $paricevuti['ordinecolonne'] : null;
        if (!isset($ordinecolonne)) {
            $ordinecolonne = GrigliaUtils::ordinecolonne($paricevuti);
        }

        return $ordinecolonne;
    }

    public static function getCampiEsclusiTestataPerGriglia($paricevuti)
    {
        return isset($paricevuti['escludere']) ? $paricevuti['escludere'] : null;
    }

    public static function getParametriCampiExtraTestataPerGriglia($paricevuti)
    {
        return isset($paricevuti['campiextra']) ? $paricevuti['campiextra'] : null;
    }

    public static function getDoctrineByEm($parametri)
    {
        if (isset($parametri['em'])) {
            $doctrine = $parametri['container']->get('doctrine')->getManager($parametri['em']);
        } else {
            $doctrine = $parametri['container']->get('doctrine')->getManager();
        }

        return $doctrine;
    }

    public static function getDoctrineFiCoreByEm($parametri, $doctrine)
    {
        if (isset($parametri['emficore'])) {
            $doctrineficore = $parametri['container']->get('doctrine')->getManager($parametri['emficore']);
        } else {
            $doctrineficore = &$doctrine;
        }

        return $doctrineficore;
    }

    public static function getOuputType($parametri)
    {
        if ((isset($parametri['output'])) && ($parametri['output'] == 'stampa')) {
            $output = 'stampa';
        } else {
            $output = 'index';
        }

        return $output;
    }

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

<?php

/* Qui Bundle */
//namespace Fi\DemoBundle\Controller;

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Fi\CoreBundle\Controller\FiController;
use Fi\CoreBundle\Controller\griglia;

/**
 * opzioniTabella controller.
 *
 */
/* Qui Tabella */
class opzioniTabellaController extends FiController {

    /**
     * Lists all opzioniTabella entities.
     *
     */
    /* @var $em \Doctrine\ORM\EntityManager */
    public function indexAction(Request $request) {
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . "Bundle";

        $em = $container->get("doctrine")->getManager();
        $entities = $em->getRepository($nomebundle . ':' . $controller)->findAll();

        $dettaglij = array(
            "descrizione" => array(array("nomecampo" => "descrizione", "lunghezza" => "600", "descrizione" => "Descrizione", "tipo" => "text")),
            "tabelle_id" => array(array("nomecampo" => "tabelle.nometabella", "lunghezza" => "400", "descrizione" => "Tabella", "tipo" => "text")),
                /* ,
                  array("nomecampo" => "ffprincipale.id", "lunghezza" => "40", "descrizione" => "IdP", "tipo" => "integer") */
        );

        $escludi = array();
        $paricevuti = array("nomebundle" => $nomebundle, "nometabella" => $controller, "dettaglij" => $dettaglij, "escludere" => $escludi, "container" => $container);

        $testatagriglia = griglia::testataPerGriglia($paricevuti);

        $testatagriglia["multisearch"] = 1;
        $testatagriglia["showconfig"] = 1;
        $testatagriglia["showadd"] = 1;
        $testatagriglia["showedit"] = 1;
        $testatagriglia["showdel"] = 1;
        $testatagriglia["editinline"] = 0;

        $testatagriglia["parametritesta"] = json_encode($paricevuti);
        $this->setParametriGriglia(array("request" => $request));
        $testatagriglia["parametrigriglia"] = json_encode(self::$parametrigriglia);

        $testata = json_encode($testatagriglia);

        return $this->render($nomebundle . ':' . $controller . ':index.html.twig', array(
                    'entities' => $entities,
                    'nomecontroller' => $controller,
                    'testata' => $testata,
        ));
    }
    
        public function setParametriGriglia($prepar = array()) {
        self::setup($prepar["request"]);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . "Bundle";
        $escludi = array();
        $tabellej["tabelle_id"] = array("tabella" => "tabelle", "campi" => array("nometabella"));

        $paricevuti = array("container" => $this->container, "nomebundle" => $nomebundle, "tabellej" => $tabellej, "nometabella" => $controller, "escludere" => $escludi);

        if ($prepar)
            $paricevuti = array_merge($paricevuti, $prepar);

        self::$parametrigriglia = $paricevuti;
    }



}

<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Fi\CoreBundle\Controller\FiController;
use Fi\CoreBundle\Controller\griglia;
use Fi\CoreBundle\Entity\permessi;
use Fi\CoreBundle\Form\permessiType;

/**
 * permessi controller.
 *
 */
class permessiController extends FiController {

    /**
     * Lists all Ffprincipale entities.
     *
     */
    public function indexAction(Request $request) {
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . "Bundle";

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository($nomebundle . ':' . $controller)->findAll();

        $dettaglij = array("operatori_id" => array(
                array("nomecampo" => "operatori.username", "lunghezza" => "200", "descrizione" => "Username", "tipo" => "text"),
                array("nomecampo" => "operatori.operatore", "lunghezza" => "200", "descrizione" => "Operatore", "tipo" => "text")
            ),
            "ruoli_id" => array(
                array("nomecampo" => "ruoli.ruolo", "lunghezza" => "200", "descrizione" => "Ruolo", "tipo" => "text"))
        );

        $paricevuti = array("doctrine" => $em, "nomebundle" => $nomebundle, "nometabella" => $controller, "dettaglij" => $dettaglij, "container" => $container);
        
        $testatagriglia = array();
        $testatagriglia["multisearch"] = 1;
        $testatagriglia["showconfig"] = 1;

        $testatagriglia = griglia::testataPerGriglia($paricevuti);

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
        $tabellej["operatori_id"] = array("tabella" => "operatori", "campi" => array("username", "operatore"));
        $tabellej["ruoli_id"] = array("tabella" => "ruoli", "campi" => array("ruolo"));

        $paricevuti = array("container" => $this->container, "nomebundle" => $nomebundle, "tabellej" => $tabellej, "nometabella" => $controller, "escludere" => $escludi);

        if ($prepar)
            $paricevuti = array_merge($paricevuti, $prepar);

        self::$parametrigriglia = $paricevuti;
    }
}
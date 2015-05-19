<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Fi\CoreBundle\Controller\FiController;
use Fi\CoreBundle\Entity\tabelle;
use Fi\CoreBundle\Controller\stampatabellaController;

/**
 * tabelle controller.
 *
 */
class tabelleController extends FiController {

    public function aggiornaAction(Request $request) {

        if ($request->get("oper") == "edit") {
            $id = $request->get("id");

            $em = $this->getDoctrine()->getManager();
            $tabelle = $em->getRepository('FiCoreBundle:tabelle')->find($id);
            if ($request->get("operatori_id") !== null)
                $tabelle->setOperatoriId($request->get("operatori_id"));
            if ($request->get("nometabella") !== null)
                $tabelle->setNometabella($request->get("nometabella"));
            if ($request->get("nomecampo") !== null)
                $tabelle->setNomecampo($request->get("nomecampo"));
            if ($request->get("mostraindex") !== null)
                $tabelle->setMostraindex($request->get("mostraindex"));
            if ($request->get("ordineindex") !== null)
                $tabelle->setOrdineindex($request->get("ordineindex"));
            if ($request->get("etichettaindex") !== null)
                $tabelle->setEtichettaindex($request->get("etichettaindex"));
            if ($request->get("mostrastampa") !== null)
                $tabelle->setMostrastampa($request->get("mostrastampa"));
            if ($request->get("ordinestampa") !== null)
                $tabelle->setOrdinestampa($request->get("ordinestampa"));
            if ($request->get("larghezzastampa") !== null)
                $tabelle->setSarghezzastampa($request->get("larghezzastampa"));
            if ($request->get("etichettastampa") !== null)
                $tabelle->setEtichettastampa($request->get("etichettastampa"));

            $em->flush();
        }


        /* operatori_id int(11)
          nometabella   varchar(45)
          nomecampo     varchar(45)
          mostraindex   tinyint(1)
          ordineindex   int(11)
          larghezzaindex        int(11)
          etichettaindex        varchar(255)
          mostrastampa  tinyint(1)
          ordinestampa  int(11)
          larghezzastampa       int(11)
          etichettastampa       varchar(255)
         *
         */

        return new Response("OK");
    }

    public function configuraAction(Request $request, $nometabella) {
        parent::setup($request);
        $gestionepermessi = new gestionepermessiController();
        $gestionepermessi->setContainer($this->container);
        $operatore = $gestionepermessi->utentecorrenteAction();
        $this->generaDB(array("tabella" => $nometabella), $request);
        $this->generaDB(array("tabella" => $nometabella, "operatore" => $operatore["id"]), $request);

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
            )
        );

        $paricevuti = array("doctrine" => $em, "nomebundle" => $nomebundle, "nometabella" => $controller, "dettaglij" => $dettaglij, "container" => $container);

        $paricevuti["escludere"] = array("mostrastampa", "nometabella", "ordineindex", "larghezzaindex", "etichettaindex", "etichettastampa", "ordinestampa", "larghezzastampa", "operatori_id");

        $testata = griglia::testataPerGriglia($paricevuti);


        $testata["multisearch"] = 0;
        $testata["showdel"] = 0;
        $testata["showadd"] = 0;
        $testata["showedit"] = 0;
        $testata["editinline"] = 1;
        $testata["nomelist"] = "#listconfigura";
        $testata["nomepager"] = "#pagerconfigura";
        $testata["tastochiudi"] = 1;
        $testata["div"] = "#dettaglioconf";
        $testata["chiamante"] = $nometabella;
        $testata["percorsogriglia"] = $nometabella . "/grigliapopup";
        $testata["altezzagriglia"] = "300";
        $testata["larghezzagriglia"] = "700";

        $testata["permessiedit"] = 1;
        $testata["permessidelete"] = 1;
        $testata["permessicreate"] = 1;
        $testata["permessiread"] = 1;


        return $this->render($nomebundle . ':' . $controller . ':configura.html.twig', array(
                    'entities' => $entities,
                    'nomecontroller' => $controller,
                    'testata' => json_encode($testata),
                    'chiamante' => $nometabella
        ));
    }

    public function generaDB($parametri = array(), Request $request) {
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . "Bundle";

        if (!isset($parametri["tabella"]))
            return false;

        $nometabella = $parametri["tabella"];
        $em = $this->getDoctrine()->getEntityManager();




        $bundles = $this->get('kernel')->getBundles();
        $bundleName = '';
        foreach ($bundles as $type => $bundle) {
            $className = get_class($bundle);
            $entityClass = substr($className, 0, strrpos($className, '\\'));
            $tableClassName = "\\" . $entityClass . "\\Entity\\" . $nometabella;
            if (!class_exists($tableClassName)) {
                $tableClassName = '';
                continue;
            } else {
                break;
            }
        }

        if (!$tableClassName) {
            throw new \Exception("Entity per la tabella " . $nometabella . " non trovata", '-1');
        }

        $bundleClass = str_replace('\\', '', $entityClass);

        $c = $em->getClassMetadata($bundleClass . ':' . $nometabella);

        $colonne = $c->getColumnNames();

        foreach ($colonne as $colonna) {

            $vettorericerca = array(
                "nometabella" => $nometabella,
                "nomecampo" => $colonna
            );


            if (isset($parametri["operatore"])) {
                $vettorericerca["operatori_id"] = $parametri["operatore"];
            }

            $trovato = $this->getDoctrine()->getRepository($nomebundle . ':tabelle')
                    ->findBy($vettorericerca, array());


            if (!$trovato) {

                $crea = new tabelle();
                $crea->setNometabella($nometabella);
                $crea->setNomecampo($colonna);

                if (isset($parametri["operatore"])) {

                    $crea->setOperatori($this->getDoctrine()->getRepository($nomebundle . ':operatori')
                                    ->findOneBy(array("id" => $parametri["operatore"]), array()));

                    unset($vettorericerca["operatori_id"]);
                    $vettorericerca["operatori_id"] = null;
                    $ritrovato = $this->getDoctrine()->getRepository($nomebundle . ':tabelle')
                            ->findOneBy($vettorericerca, array());

                    if ($ritrovato) {

                        $crea->setMostrastampa($ritrovato->getMostrastampa() ? true : false);
                        $crea->setMostraindex($ritrovato->getMostraindex() ? true : false);
                    }
                } else {
                    $crea->setMostrastampa(true);
                    $crea->setMostraindex(true);
                }

                $ma = $this->getDoctrine()->getManager();
                $ma->persist($crea);
                $ma->flush();
            }
        }
    }

    public function grigliapopupAction(Request $request, $chiamante) {

//var_dump($chiamante);
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . "Bundle";
        $em = $this->getDoctrine()->getManager();

        $gestionepermessi = new gestionepermessiController();
        $gestionepermessi->setContainer($this->container);
        $operatore = $gestionepermessi->utentecorrenteAction();

        $tabellej["operatori_id"] = array("tabella" => "operatori", "campi" => array("username", "operatore"));

        $paricevuti = array("request" => $request, "doctrine" => $em, "container" => $this->container, "nomebundle" => $nomebundle, "nometabella" => $controller, "tabellej" => $tabellej);

        $paricevuti["escludere"] = array("mostrastampa", "nometabella", "ordineindex", "larghezzaindex", "etichettaindex", "etichettastampa", "ordinestampa", "larghezzastampa", "operatori_id");
        $paricevuti["precondizioni"] = array("tabelle.nometabella" => $chiamante, "tabelle.operatori_id" => $operatore["id"]);


        return new Response(griglia::datiPerGriglia($paricevuti));
    }

    /**
     * Creates a new Ffprincipale entity.
     *
     */
    public function grigliaAction(Request $request) {
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . "Bundle";
        $em = $this->getDoctrine()->getManager();

        $tabellej["operatori_id"] = array("tabella" => "operatori", "campi" => array("username", "operatore"));

        $paricevuti = array("request" => $request, "doctrine" => $em, "nomebundle" => $nomebundle, "nometabella" => $controller, "tabellej" => $tabellej, "container" => $this->container);

        return new Response(griglia::datiPerGriglia($paricevuti));
    }

}

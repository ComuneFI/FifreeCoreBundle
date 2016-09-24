<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Fi\CoreBundle\Entity\tabelle;

/**
 * Tabelle controller.
 */
class TabelleController extends FiController {

    public function aggiornaAction(Request $request) {
        if ($request->get('oper') == 'edit') {
            $gestionepermessi = new GestionepermessiController();
            $gestionepermessi->setContainer($this->container);
            $operatore = $gestionepermessi->utentecorrenteAction();

            $id = $request->get('id');

            $em = $this->getDoctrine()->getManager();
            $tabelle = $em->getRepository('FiCoreBundle:Tabelle')->find($id);
            /* if ($request->get("operatori_id") !== NULL) {
              $tabelle->setOperatoriId($request->get("operatori_id"));
              } else {
              $tabelle->setOperatoriId(NULL);
              } */
            $tabelle->setOperatoriId($operatore['id']);
            $nometabella = $this->getRequestValue($request, 'nometabella');
            if ($nometabella) {
                $tabelle->setNometabella($nometabella);
            }
            $nomecampo = $this->getRequestValue($request, 'nomecampo');
            if ($nomecampo) {
                $tabelle->setNomecampo($nomecampo);
            }
            $mostraindex = $this->getRequestValue($request, 'mostraindex');
            if ($mostraindex) {
                $tabelle->setMostraindex($mostraindex);
            }
            $ordineindex = $this->getRequestValue($request, 'ordineindex');
            if ($ordineindex) {
                $tabelle->setOrdineindex($ordineindex);
            }
            $etichettaindex = $this->getRequestValue($request, 'etichettaindex');
            $tabelle->setEtichettaindex($etichettaindex);

            $larghezzaindex = $this->getRequestValue($request, 'larghezzaindex');
            $tabelle->setLarghezzaindex($larghezzaindex);

            $mostrastampa = $this->getRequestValue($request, 'mostrastampa');
            if ($mostrastampa) {
                $tabelle->setMostrastampa($mostrastampa);
            }

            $ordinestampa = $this->getRequestValue($request, 'ordinestampa');
            $tabelle->setOrdinestampa($ordinestampa);

            $etichettastampa = $this->getRequestValue($request, 'etichettastampa');
            $tabelle->setEtichettastampa($etichettastampa);

            $larghezzastampa = $this->getRequestValue($request, 'larghezzastampa');
            $tabelle->setLarghezzastampa($larghezzastampa);
            $em->persist($tabelle);
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

        return new Response('OK');
    }

    private function getRequestValue($request, $attribute) {
        if (($request->get($attribute) !== null) && ($request->get($attribute) !== '')) {
            return $request->get($attribute);
        } else {
            return null;
        }
    }

    public function configuraAction(Request $request, $nometabella) {
        parent::setup($request);
        $gestionepermessi = new GestionepermessiController();
        $gestionepermessi->setContainer($this->container);
        $operatore = $gestionepermessi->utentecorrenteAction();
        $this->generaDB(array('tabella' => $nometabella), $request);
        $this->generaDB(array('tabella' => $nometabella, 'operatore' => $operatore['id']), $request);

        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . 'Bundle';

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository($nomebundle . ':' . $controller)->findAll();

        $dettaglij = array(/* "operatori_id" => array(array("nomecampo" => "operatori.username", "lunghezza" => "100", "descrizione" => "Username", "tipo" => "text"),
              array("nomecampo" => "operatori.operatore", "lunghezza" => "100", "descrizione" => "Operatore", "tipo" => "text")), */
            'nomecampo' => array(array('nomecampo' => 'nomecampo', 'lunghezza' => '150', 'descrizione' => 'Campo', 'tipo' => 'text', 'editable' => false)),
            'mostraindex' => array(array('nomecampo' => 'mostraindex', 'lunghezza' => '100', 'descrizione' => 'Vedi in griglia', 'tipo' => 'boolean')),
            'ordineindex' => array(array('nomecampo' => 'ordineindex', 'lunghezza' => '100', 'descrizione' => 'Ordine in griglia', 'tipo' => 'text')),
            'etichettaindex' => array(array('nomecampo' => 'etichettaindex', 'lunghezza' => '150', 'descrizione' => 'Label in griglia', 'tipo' => 'text')),
            'larghezzaindex' => array(array('nomecampo' => 'larghezzaindex', 'lunghezza' => '100', 'descrizione' => 'Largh. in griglia', 'tipo' => 'text')),
            'mostrastampa' => array(array('nomecampo' => 'mostrastampa', 'lunghezza' => '100', 'descrizione' => 'Vedi in stampa', 'tipo' => 'boolean')),
            'ordinestampa' => array(array('nomecampo' => 'ordinestampa', 'lunghezza' => '100', 'descrizione' => 'Ordine in stampa', 'tipo' => 'text')),
            'etichettastampa' => array(array('nomecampo' => 'etichettastampa', 'lunghezza' => '150', 'descrizione' => 'Label in stampa', 'tipo' => 'text')),
            'larghezzastampa' => array(array('nomecampo' => 'larghezzastampa', 'lunghezza' => '100', 'descrizione' => 'Largh. in stampa', 'tipo' => 'text')),
        );

        $paricevuti = array('doctrine' => $em, 'nomebundle' => $nomebundle, 'nometabella' => $controller, 'dettaglij' => $dettaglij, 'container' => $container);

        //$paricevuti["escludere"] = array("mostrastampa", "nometabella", "ordineindex", "larghezzaindex", "etichettaindex", "etichettastampa", "ordinestampa", "larghezzastampa", "operatori_id");
        $paricevuti['escludere'] = array('nometabella', 'operatori_id');

        $testata = Griglia::testataPerGriglia($paricevuti);

        $testata['titolo'] = "Configurazione colonne per tabella $nometabella";
        $testata['multisearch'] = 0;
        $testata['showdel'] = 0;
        $testata['showadd'] = 0;
        $testata['showedit'] = 0;
        $testata['showprint'] = 0;
        $testata['editinline'] = 1;
        $testata['nomelist'] = '#listconfigura';
        $testata['nomepager'] = '#pagerconfigura';
        $testata['tastochiudi'] = 1;
        $testata['div'] = '#dettaglioconf';
        $testata['chiamante'] = $nometabella;
        $testata['percorsogriglia'] = $nometabella . '/grigliapopup';
        $testata['altezzagriglia'] = '300';
        $testata['larghezzagriglia'] = '900';

        $testata['permessiedit'] = 1;
        $testata['permessidelete'] = 1;
        $testata['permessicreate'] = 1;
        $testata['permessiread'] = 1;

        return $this->render(
                        $nomebundle . ':' . $controller . ':configura.html.twig', array(
                    'entities' => $entities,
                    'nomecontroller' => $controller,
                    'testata' => json_encode($testata),
                    'chiamante' => $nometabella,
                        )
        );
    }

    public function generaDB($parametri, Request $request) {
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();

        $nomebundle = $namespace . $bundle . 'Bundle';

        if (!isset($parametri['tabella'])) {
            return false;
        }

        $nometabella = $parametri['tabella'];
        $em = $this->getDoctrine()->getEntityManager();

        $bundles = $this->get('kernel')->getBundles();
        foreach ($bundles as $bundle) {
            $className = get_class($bundle);
            $entityClass = substr($className, 0, strrpos($className, '\\'));
            $tableClassName = '\\' . $entityClass . '\\Entity\\' . $nometabella;
            if (!class_exists($tableClassName)) {
                $tableClassName = '';
                continue;
            } else {
                break;
            }
        }

        if (!$tableClassName) {
            throw new \Exception('Entity per la tabella ' . $nometabella . ' non trovata', '-1');
        }

        $bundleClass = str_replace('\\', '', $entityClass);

        $c = $em->getClassMetadata($bundleClass . ':' . $nometabella);

        $colonne = $c->getColumnNames();

        foreach ($colonne as $colonna) {
            $vettorericerca = array(
                'nometabella' => $nometabella,
                'nomecampo' => $colonna,
            );

            if (isset($parametri['operatore'])) {
                $vettorericerca['operatori_id'] = $parametri['operatore'];
            }

            $trovato = $this->getDoctrine()->getRepository($nomebundle . ':tabelle')->findBy($vettorericerca, array());

            if (!$trovato) {
                $crea = new tabelle();
                $crea->setNometabella($nometabella);
                $crea->setNomecampo($colonna);

                if (isset($parametri['operatore'])) {
                    $crea->setOperatori($this->getDoctrine()->getRepository($nomebundle . ':operatori')->findOneBy(array('id' => $parametri['operatore']), array()));

                    unset($vettorericerca['operatori_id']);
                    $vettorericerca['operatori_id'] = null;
                    $ritrovato = $this->getDoctrine()->getRepository($nomebundle . ':tabelle')->findOneBy($vettorericerca, array());

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
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $em = $this->getDoctrine()->getManager();

        $gestionepermessi = new GestionepermessiController();
        $gestionepermessi->setContainer($this->container);
        $operatore = $gestionepermessi->utentecorrenteAction();
        
        $tabellej['operatori_id'] = array('tabella' => 'operatori', 'campi' => array('username', 'operatore'));

        $paricevuti = array('request' => $request, 'doctrine' => $em, 'container' => $this->container, 'nomebundle' => $nomebundle, 'nometabella' => $controller, 'tabellej' => $tabellej);

        //$paricevuti["escludere"] = array("mostrastampa", "nometabella", "ordineindex", "larghezzaindex", "etichettaindex", "etichettastampa", "ordinestampa", "larghezzastampa", "operatori_id");
        $paricevuti['escludere'] = array('nometabella', 'operatori_id');
        $paricevuti['precondizioni'] = array('Tabelle.nometabella' => $chiamante, 'Tabelle.operatori_id' => $operatore['id']);

        return new Response(Griglia::datiPerGriglia($paricevuti));
    }

    /**
     * Creates a new Ffprincipale entity.
     */
    public function grigliaAction(Request $request) {
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $em = $this->getDoctrine()->getManager();

        $tabellej['operatori_id'] = array('tabella' => 'operatori', 'campi' => array('username', 'operatore'));

        $paricevuti = array('request' => $request, 'doctrine' => $em, 'nomebundle' => $nomebundle, 'nometabella' => $controller, 'tabellej' => $tabellej, 'container' => $this->container);

        return new Response(Griglia::datiPerGriglia($paricevuti));
    }

    public function listacampitabellaAction(Request $request) {
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $nomebundle = $namespace . $bundle . 'Bundle';

        $nometabella = trim($request->get('tabella'));
        if (!isset($nometabella)) {
            return false;
        }

        $escludiid = $request->get('escludiid');
        if (!isset($escludiid)) {
            $escludiid = 0;
        }

        $em = $this->getDoctrine()->getEntityManager();

        $bundles = $this->get('kernel')->getBundles();
        foreach ($bundles as $type => $bundle) {
            $className = get_class($bundle);
            $entityClass = substr($className, 0, strrpos($className, '\\'));
            $tableClassName = '\\' . $entityClass . '\\Entity\\' . $nometabella;
            if (!class_exists($tableClassName)) {
                $tableClassName = '';
                continue;
            } else {
                break;
            }
        }

        if (!$tableClassName) {
            throw new \Exception('Entity per la tabella ' . $nometabella . ' non trovata', '-1');
        }

        $bundleClass = str_replace('\\', '', $entityClass);
        $c = $em->getClassMetadata($bundleClass . ':' . $nometabella);
        $colonne = $c->getColumnNames();

        $risposta = array();
        if ($escludiid == 1) {
            $gestionepermessi = new GestionepermessiController();
            $gestionepermessi->setContainer($this->container);
            $operatore = $gestionepermessi->utentecorrenteAction();
            foreach ($colonne as $colonna) {
                $nomecampo = trim(strtolower($colonna));
                if (($nomecampo !== 'id') && (strpos($colonna, '_id') == false)) {
                    $qb = $this->getDoctrine()->getRepository("$nomebundle:$controller")
                            ->createQueryBuilder('t')
                            ->where('LOWER(t.nometabella) = :nometabella')
                            ->andWhere('LOWER(t.nomecampo) = :nomecampo')
                            ->andWhere('t.operatori_id = :operatori_id')
                            ->setParameter('nometabella', $nometabella)
                            ->setParameter('nomecampo', $nomecampo)
                            ->setParameter('operatori_id', $operatore['id'])
                            ->getQuery();
                    $labeltrovata = $qb->getResult();
                    if (!$labeltrovata) {
                        $qb = $this->getDoctrine()->getRepository("$nomebundle:$controller")
                                ->createQueryBuilder('t')
                                ->where('LOWER(t.nometabella) = :nometabella')
                                ->andWhere('LOWER(t.nomecampo) = :nomecampo')
                                ->andWhere('t.operatori_id IS NULL')
                                ->setParameter('nometabella', $nometabella)
                                ->setParameter('nomecampo', $nomecampo)
                                ->getQuery();
                        $labeltrovata = $qb->getResult();
                        if (!$labeltrovata) {
                            $risposta[$colonna] = $colonna;
                        } else {
                            if (($labeltrovata[0]->getEtichettaindex()) && ($labeltrovata[0]->getEtichettaindex() != '')) {
                                $risposta[$colonna] = trim($labeltrovata[0]->getEtichettaindex());
                            } else {
                                $risposta[$colonna] = $colonna;
                            }
                        }
                    } else {
                        if (($labeltrovata[0]->getEtichettaindex()) && ($labeltrovata[0]->getEtichettaindex() != '')) {
                            $risposta[$colonna] = trim($labeltrovata[0]->getEtichettaindex());
                        } else {
                            $risposta[$colonna] = $colonna;
                        }
                    }
                }
            }
        } else {
            foreach ($colonne as $colonna) {
                $risposta[$colonna] = $colonna;
            }
        }
        //natcasesort($risposta);
        asort($risposta, SORT_NATURAL | SORT_FLAG_CASE);

        return new JsonResponse($risposta);
    }

}

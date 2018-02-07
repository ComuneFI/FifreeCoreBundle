<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Fi\CoreBundle\Entity\Tabelle;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Tabelle controller.
 */
class TabelleController extends FiCoreController
{

    public function aggiornaAction(Request $request)
    {
        if ($request->get('oper') == 'edit') {
            $gestionepermessi = $this->get("ficorebundle.gestionepermessi");
            $operatore = $gestionepermessi->utentecorrente();

            $id = $request->get('id');

            $em = $this->getDoctrine()->getManager();
            $tabelle = $em->getRepository('FiCoreBundle:Tabelle')->find($id);
            if (!$tabelle) {
                throw new AccessDeniedException("Oggetto non trovato");
            }
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
            $tabelle->setMostraindex($mostraindex);
            $ordineindex = $this->getRequestValue($request, 'ordineindex');
            $tabelle->setOrdineindex($ordineindex);
            $etichettaindex = $this->getRequestValue($request, 'etichettaindex');
            $tabelle->setEtichettaindex($etichettaindex);

            $larghezzaindex = $this->getRequestValue($request, 'larghezzaindex');
            $tabelle->setLarghezzaindex($larghezzaindex);

            $mostrastampa = $this->getRequestValue($request, 'mostrastampa');
            $tabelle->setMostrastampa($mostrastampa);

            $ordinestampa = $this->getRequestValue($request, 'ordinestampa');
            $tabelle->setOrdinestampa($ordinestampa);

            $etichettastampa = $this->getRequestValue($request, 'etichettastampa');
            $tabelle->setEtichettastampa($etichettastampa);

            $larghezzastampa = $this->getRequestValue($request, 'larghezzastampa');
            $tabelle->setLarghezzastampa($larghezzastampa);
            $em->persist($tabelle);
            $em->flush();
        }

        return new Response('OK');
    }

    private function getRequestValue($request, $attribute)
    {
        if (($request->get($attribute) !== null) && ($request->get($attribute) !== '')) {
            return $request->get($attribute);
        } else {
            return null;
        }
    }

    public function configuraAction(Request $request, $nometabella)
    {
        $this->setup($request);
        $gestionepermessi = $this->get("ficorebundle.gestionepermessi");
        $operatore = $gestionepermessi->utentecorrente();
        $this->generaDB(array('tabella' => $nometabella), $request);
        $this->generaDB(array('tabella' => $nometabella, 'operatore' => $operatore['id']), $request);

        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . 'Bundle';

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository($nomebundle . ':' . $controller)->findAll();

        $dettaglij = array(
            'nomecampo' => array(
                array('nomecampo' => 'nomecampo', 'lunghezza' => '150', 'descrizione' => 'Campo', 'tipo' => 'text', 'editable' => false),),
            'mostraindex' => array(
                array('nomecampo' => 'mostraindex', 'lunghezza' => '100', 'descrizione' => 'Vedi in griglia', 'tipo' => 'boolean'),
            ),
            'ordineindex' => array(
                array('nomecampo' => 'ordineindex', 'lunghezza' => '100', 'descrizione' => 'Ordine in griglia', 'tipo' => 'text'),
            ),
            'etichettaindex' => array(
                array('nomecampo' => 'etichettaindex', 'lunghezza' => '150', 'descrizione' => 'Label in griglia', 'tipo' => 'text'),
            ),
            'larghezzaindex' => array(
                array('nomecampo' => 'larghezzaindex', 'lunghezza' => '100', 'descrizione' => 'Largh. in griglia', 'tipo' => 'text'),
            ),
            'mostrastampa' => array(
                array('nomecampo' => 'mostrastampa', 'lunghezza' => '100', 'descrizione' => 'Vedi in stampa', 'tipo' => 'boolean'),
            ),
            'ordinestampa' => array(
                array('nomecampo' => 'ordinestampa', 'lunghezza' => '100', 'descrizione' => 'Ordine in stampa', 'tipo' => 'text'),
            ),
            'etichettastampa' => array(
                array('nomecampo' => 'etichettastampa', 'lunghezza' => '150', 'descrizione' => 'Label in stampa', 'tipo' => 'text'),
            ),
            'larghezzastampa' => array(
                array('nomecampo' => 'larghezzastampa', 'lunghezza' => '100', 'descrizione' => 'Largh. in stampa', 'tipo' => 'text'),
            ),
        );

        $paricevuti = array(
            'doctrine' => $em,
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'dettaglij' => $dettaglij,
            'container' => $container,
        );

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
        $twigparm = array(
            'entities' => $entities,
            'nomecontroller' => $controller,
            'testata' => json_encode($testata),
            'chiamante' => $nometabella,
        );

        return $this->render($nomebundle . ':' . $controller . ':configura.html.twig', $twigparm);
    }

    public function generaDB($parametri, Request $request)
    {
        if (!isset($parametri['tabella'])) {
            return false;
        }

        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();

        $nomebundle = $namespace . $bundle . 'Bundle';

        $nometabella = $parametri['tabella'];
        $em = $this->getDoctrine()->getManager();

        $bundles = $this->get('kernel')->getBundles();
        $tableClassName = "";
        $entityClass = "";
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

        if (!$entityClass) {
            throw new \Exception('Entity class per la tabella ' . $nometabella . ' non trovata', '-1');
        }

        $bundleClass = str_replace('\\', '', $entityClass);

        $c = $em->getClassMetadata($bundleClass . ':' . $nometabella);

        $colonne = $c->getColumnNames();
        $this->scriviDB($colonne, $nometabella, $nomebundle, $parametri);
    }

    private function scriviDB($colonne, $nometabella, $nomebundle, $parametri)
    {
        foreach ($colonne as $colonna) {
            $vettorericerca = array(
                'nometabella' => $nometabella,
                'nomecampo' => $colonna,
            );

            if (isset($parametri['operatore'])) {
                $vettorericerca['operatori_id'] = $parametri['operatore'];
            }

            $trovato = $this->getDoctrine()->getRepository($nomebundle . ':Tabelle')->findBy($vettorericerca, array());

            if (empty($trovato)) {
                $this->creaRecordTabelle($nometabella, $colonna, $vettorericerca, $parametri);
            }
        }
    }

    private function creaRecordTabelle($nometabella, $colonna, $vettorericerca, $parametri)
    {
        $crea = new Tabelle();
        $crea->setNometabella($nometabella);
        $crea->setNomecampo($colonna);

        if (isset($parametri['operatore'])) {
            $idOperatore = $parametri['operatore'];
            $creaoperatore = $this->getDoctrine()->getRepository('FiCoreBundle:Operatori')->find($idOperatore);
            if ($creaoperatore instanceof \Fi\CoreBundle\Entity\Operatori) {
                $crea->setOperatori($creaoperatore);
            }

            $vettorericerca['operatori_id'] = null;
            $ritrovato = $this->getDoctrine()->getRepository('FiCoreBundle:Tabelle')->findOneBy($vettorericerca);

            if (!empty($ritrovato)) {
                $crea->setMostrastampa($ritrovato->hasMostrastampa() ? true : false);
                $crea->setMostraindex($ritrovato->hasMostraindex() ? true : false);
            }
        } else {
            $crea->setMostrastampa(true);
            $crea->setMostraindex(true);
        }

        $ma = $this->getDoctrine()->getManager();
        $ma->persist($crea);
        $ma->flush();
    }

    public function grigliapopupAction(Request $request, $chiamante)
    {
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $em = $this->getDoctrine()->getManager();

        $gestionepermessi = $this->get("ficorebundle.gestionepermessi");
        $operatore = $gestionepermessi->utentecorrente();
        $tabellej = array();
        $tabellej['operatori_id'] = array('tabella' => 'operatori', 'campi' => array('username', 'operatore'));

        $paricevuti = array(
            'request' => $request,
            'doctrine' => $em,
            'container' => $this->container,
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'tabellej' => $tabellej,);

        $paricevuti['escludere'] = array('nometabella', 'operatori_id');
        $paricevuti['precondizioni'] = array('Tabelle.nometabella' => $chiamante, 'Tabelle.operatori_id' => $operatore['id']);

        return new Response(Griglia::datiPerGriglia($paricevuti));
    }

    protected function setParametriGriglia($prepar = array())
    {
        $this->setup($prepar['request']);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $gestionepermessi = $this->get("ficorebundle.gestionepermessi");
        $canRead = ($gestionepermessi->leggere(array('modulo' => $controller)) ? 1 : 0);
        if (!$canRead) {
            throw new AccessDeniedException("Non si hanno i permessi per visualizzare questo contenuto");
        }

        $nomebundle = $namespace . $bundle . 'Bundle';
        $tabellej = array();
        $tabellej['operatori_id'] = array('tabella' => 'operatori', 'campi' => array('username'));
        $escludi = array("operatori"); //'operatori_id'

        $paricevuti = array(
            'container' => $this->container,
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'tabellej' => $tabellej,
            'escludere' => $escludi
        );

        if (!empty($prepar)) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }

    public function listacampitabellaAction(Request $request)
    {
        $this->setup($request);
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

        $em = $this->getDoctrine()->getManager();
        $tableClassName = "";
        $entityClass = "";
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

        if (!$entityClass) {
            throw new \Exception('Entity class per la tabella ' . $nometabella . ' non trovata', '-1');
        }

        $bundleClass = str_replace('\\', '', $entityClass);
        $c = $em->getClassMetadata($bundleClass . ':' . $nometabella);
        $colonne = $c->getColumnNames();

        $risposta = $this->listacampitabelladettagli($escludiid, $colonne, $nomebundle, $controller);
        //natcasesort($risposta);
        asort($risposta, SORT_NATURAL | SORT_FLAG_CASE);

        return new JsonResponse($risposta);
    }

    private function listacampitabelladettagli($escludiid, $colonne, $nomebundle, $controller)
    {
        $risposta = array();
        $nometabella = $controller;
        if ($escludiid == 1) {
            $gestionepermessi = $this->get("ficorebundle.gestionepermessi");
            $operatore = $gestionepermessi->utentecorrente();
            foreach ($colonne as $colonna) {
                $nomecampo = trim(strtolower($colonna));
                if (($nomecampo !== 'id') && (strpos($colonna, '_id') === false)) {
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

        return $risposta;
    }
}

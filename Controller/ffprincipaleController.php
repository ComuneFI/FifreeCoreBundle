<?php

namespace Fi\CoreBundle\Controller;

use Fi\CoreBundle\Controller\FiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Fi\CoreBundle\Controller\griglia;

/**
 * Ffprincipale controller.
 *
 */
class ffprincipaleController extends FiController {

  /**
   * Lists all tables entities.
   *
   */
  /* @var $em \Doctrine\ORM\EntityManager */
  public function indexAction(Request $request) {
    self::setup($request);
    $namespace = $this->getNamespace();
    $bundle = $this->getBundle();
    $controller = $this->getController();
    $container = $this->container;

    $gestionepermessi = new gestionepermessiController();
    $gestionepermessi->setContainer($this->container);
    $utentecorrente = $gestionepermessi->utentecorrenteAction();
    $canRead = ($gestionepermessi->leggereAction(array("modulo" => $controller)) ? 1 : 0);


    $nomebundle = $namespace . $bundle . "Bundle";

    $em = $container->get("doctrine")->getManager();
    $entities = $em->getRepository($nomebundle . ':' . $controller)->findAll();

    $paricevuti = array("nomebundle" => $nomebundle, "nometabella" => $controller, "container" => $container);

    $testatagriglia = griglia::testataPerGriglia($paricevuti);

    $testatagriglia["multisearch"] = 1;
    $testatagriglia["showconfig"] = 1;

    $testatagriglia["parametritesta"] = json_encode($paricevuti);

    $this->setParametriGriglia(array("request" => $request));

    $testatagriglia["allegati"] = 1;

    $testatagriglia["parametrigriglia"] = json_encode(self::$parametrigriglia);

    /* @var $qb \Doctrine\ORM\QueryBuilder */
    $qb = $em->createQueryBuilder();
    $qb->select(array('a'));
    $qb->from('FiCoreBundle:opzioniTabella', 'a');
    $qb->leftJoin('a.tabelle', 't');
    $qb->where('t.nometabella = :tabella');
    $qb->andWhere("t.nomecampo is null or t.nomecampo = ''");
    $qb->setParameter('tabella', $controller);
    $opzioni = $qb->getQuery()->getResult();
    foreach ($opzioni as $opzione) {
      $testatagriglia[$opzione->getParametro()] = $opzione->getValore();
    }

    $testata = json_encode($testatagriglia);

    return $this->render($nomebundle . ':' . $controller . ':index.html.twig', array(
                'entities' => $entities,
                'nomecontroller' => $controller,
                'testata' => $testata,
                'canread' => $canRead,
    ));
  }

}

<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FiController extends Controller {

    static $namespace;
    static $bundle;
    static $controller;
    static $action;
    static $parametrigriglia;

    protected function setup(Request $request) {
        $matches = array();
        self::$controller = new \ReflectionClass(get_class($this));

        preg_match('/(.*)\\\(.*)Bundle\\\Controller\\\(.*)Controller/', self::$controller->getName(), $matches);

        self::$namespace = isset($matches[1]) ? $matches[1] : "";
        self::$bundle = isset($matches[2]) ? $matches[2] : "";
        self::$controller = isset($matches[3]) ? $matches[3] : "";
        self::$action = substr($request->attributes->get('_controller'), strrpos($request->attributes->get('_controller'), ":") + 1);
    }

    public function setParametriGriglia($prepar = array()) {
        self::setup($prepar["request"]);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . "Bundle";
        $escludi = array();

        $paricevuti = array("container" => $this->container, "nomebundle" => $nomebundle, "nometabella" => $controller, "escludere" => $escludi);

        if ($prepar)
            $paricevuti = array_merge($paricevuti, $prepar);

        self::$parametrigriglia = $paricevuti;
    }

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
//        $utentecorrente = $gestionepermessi->utentecorrenteAction();
        $canRead = ($gestionepermessi->leggereAction(array("modulo" => $controller)) ? 1 : 0);
        $idpassato = $request->get("id");

        $nomebundle = $namespace . $bundle . "Bundle";

        $em = $container->get("doctrine")->getManager();
//        $entities = $em->getRepository($nomebundle . ':' . $controller)->findAll();

        $paricevuti = array("nomebundle" => $nomebundle, "nometabella" => $controller, "container" => $container);

        $testatagriglia = griglia::testataPerGriglia($paricevuti);

        $testatagriglia["multisearch"] = 1;
        $testatagriglia["showconfig"] = 1;

        $testatagriglia["parametritesta"] = json_encode($paricevuti);

        $this->setParametriGriglia(array("request" => $request));
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
//                    'entities' => $entities,
                    'nomecontroller' => $controller,
                    'testata' => $testata,
                    'canread' => $canRead,
                    'idpassato' => $idpassato
        ));
    }

    public function grigliaAction(Request $request) {

        $this->setParametriGriglia(array("request" => $request));
        $paricevuti = self::$parametrigriglia;
        return new Response(griglia::datiPerGriglia($paricevuti));
    }

    /**
     * Creates a new table entity.
     *
     */
    public function createAction(Request $request) {
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . "Bundle";
        $classbundle = $namespace . "\\" . $bundle . "Bundle" . "\\Entity\\" . $controller;
        $formbundle = $namespace . "\\" . $bundle . "Bundle" . "\\Form\\" . $controller;

        $entity = new $classbundle();
        $formType = $formbundle . "Type";

        // Questo codice per versioni che usano un symfony inferiore a 2.8
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '2.8') >= 0) {
            $form = $this->createForm($formType, $entity);
        } else {
            $form = $this->createForm(new $formType(), $entity);
        }

        $form->submit($request->request->get($form->getName()));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $continua = $request->get("continua");
            if ($continua == 0) {
                return new Response("OK");
            } else {
                return $this->redirect($this->generateUrl($controller . '_edit', array('id' => $entity->getId())));
            }
        }

        return $this->render($nomebundle . ':' . $controller . ':new.html.twig', array(
                    'nomecontroller' => $controller,
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new table entity.
     *
     */
    public function newAction(Request $request) {
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . "Bundle";
        $classbundle = $namespace . "\\" . $bundle . "Bundle" . "\\Entity\\" . $controller;
        $formbundle = $namespace . "\\" . $bundle . "Bundle" . "\\Form\\" . $controller;
        $formType = $formbundle . "Type";

        $entity = new $classbundle();

        // Questo codice per versioni che usano un symfony inferiore a 2.8
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '2.8') >= 0) {
            $form = $this->createForm($formType, $entity, array("attr" => array(
                    'id' => "formdati" . $controller,
                ),
                'action' => $this->generateUrl($controller . "_create")
            ));
        } else {

            $form = $this->createForm(new $formType(), $entity, array("attr" => array(
                    'id' => "formdati" . $controller,
                ),
                'action' => $this->generateUrl($controller . "_create")
            ));
        }

        return $this->render($nomebundle . ':' . $controller . ':new.html.twig', array(
                    'nomecontroller' => $controller,
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing table entity.
     *
     */
    /* @var $em \Doctrine\ORM\EntityManager */
    public function editAction(Request $request, $id) {
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $allegati = ($request->get("allegati") == 1 ? 1 : 0);

        $nomebundle = $namespace . $bundle . "Bundle";
        $formbundle = $namespace . "\\" . $bundle . "Bundle" . "\\Form\\" . $controller;
        $formType = $formbundle . "Type";

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($nomebundle . ':' . $controller)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $controller . ' entity.');
        }

        // Questo codice per versioni che usano un symfony inferiore a 2.8
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '2.8') >= 0) {
            $editForm = $this->createForm($formType, $entity, array("attr" => array(
                    'id' => "formdati" . $controller,
                ),
                'action' => $this->generateUrl($controller . "_update", array("id" => $entity->getId()))
            ));
        } else {

            $editForm = $this->createForm(new $formType(), $entity, array("attr" => array(
                    'id' => "formdati" . $controller,
                ),
                'action' => $this->generateUrl($controller . "_update", array("id" => $entity->getId()))
            ));
        }

        $deleteForm = $this->createDeleteForm($id);



        return $this->render($nomebundle . ':' . $controller . ':edit.html.twig', array(
                    'entity' => $entity,
                    'nomecontroller' => $controller,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'allegati' => $allegati
        ));
    }

    /**
     * Edits an existing table entity.
     *
     */
    /* @var $em \Doctrine\ORM\EntityManager */
    public function updateAction(Request $request, $id) {
        self::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . "Bundle";
        $formbundle = $namespace . "\\" . $bundle . "Bundle" . "\\Form\\" . $controller;
        $formType = $formbundle . "Type";

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($nomebundle . ':' . $controller)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $controller . ' entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        // Questo codice per versioni che usano un symfony inferiore a 2.8
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '2.8') >= 0) {
            $editForm = $this->createForm($formType, $entity);
        } else {
            $editForm = $this->createForm(new $formType(), $entity);
        }

        $editForm->submit($request->request->get($editForm->getName()));

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $continua = $request->get("continua");
            if ($continua == 0) {
                return new Response("OK");
            } else {
                return $this->redirect($this->generateUrl($controller . '_edit', array('id' => $id)));
            }
        }

        return $this->render($nomebundle . ':' . $controller . ':edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'nomecontroller' => $controller,
        ));
    }

    /**
     * Edits an existing table entity.
     *
     */
    /* @var $em \Doctrine\ORM\EntityManager */
    public function aggiornaAction(Request $request) {
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . "Bundle";
        $formbundle = $namespace . "\\" . $bundle . "Bundle" . "\\Form\\" . $controller;
        $formType = $formbundle . "Type";

        $id = $this->get('request')->request->get('id');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($nomebundle . ':' . $controller)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $controller . ' entity.');
        }

        throw $this->createNotFoundException("Implementare a seconda dell'esigenza 'aggiornaAction' del controller " . $nomebundle . '/' . $controller);
    }

    /**
     * Deletes a table entity.
     *
     */
    /* @var $em \Doctrine\ORM\EntityManager */
    public function deleteAction(Request $request) {
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . "Bundle";

        //if (!$request->isXmlHttpRequest()) {
        //    $request->checkCSRFProtection();
        //}
        try {
            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            $ids = explode(",", $request->get("id"));
            $qb->delete($nomebundle . ':' . $controller, 'u')
                    ->andWhere('u.id IN (:ids)')
                    ->setParameter('ids', $ids);

            $query = $qb->getQuery();
            $query->execute();
        } catch (\Exception $e) {
            $response = new Response();
            $response->setStatusCode("200");
            return new Response("404");
        }
        return new Response("OK");
    }

    /**
     * Creates a form to delete a table entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id) {
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '2.8') >= 0) {
            return $this->createFormBuilder(array('id' => $id))
                            ->add('id', get_class(new \Symfony\Component\Form\Extension\Core\Type\HiddenType))
                            ->getForm();
        } else {
            return $this->createFormBuilder(array('id' => $id))
                            ->add('id', "hidden")
                            ->getForm();
        }
    }

    public function stampatabellaAction(Request $request) {
        self::setup($request);
        $pdf = new stampatabellaController($this->container);

        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . "Bundle";


        $em = $this->getDoctrine()->getManager();

        $paricevuti = array("nomebundle" => $nomebundle, "nometabella" => $request->get("nometabella"), "container" => $container, "request" => $request);

        if ($request->get("parametritesta")) {
            $parametritesta = get_object_vars(json_decode($request->get("parametritesta")));
            $parametritesta["container"] = $container;
            $parametritesta["doctrine"] = $em;
            $parametritesta["request"] = $request;
            $parametritesta["output"] = "stampa";
        }

        $testatagriglia = griglia::testataPerGriglia($request->get("parametritesta") ? $parametritesta : $paricevuti);
        if ($request->get("titolo")) {
            $testatagriglia["titolo"] = $request->get("titolo");
        }

        if ($request->get("parametrigriglia")) {
            $parametrigriglia = get_object_vars(json_decode($request->get("parametrigriglia")));
            $parametrigriglia["container"] = $container;
            $parametrigriglia["doctrine"] = $em;
            $parametrigriglia["request"] = $request;
            $parametrigriglia["output"] = "stampa";
        }

        $corpogriglia = griglia::datiPerGriglia($request->get("parametrigriglia") ? $parametrigriglia : $paricevuti);

        $parametri = array("request" => $request, "testata" => $testatagriglia, "griglia" => $corpogriglia);

        $pdf->stampa($parametri);

        return new Response("OK");
    }

    public function esportaexcelAction(Request $request) {
        self::setup($request);
        $xls = new stampatabellaController($this->container);

        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;
        $nomebundle = $namespace . $bundle . "Bundle";


        $em = $this->getDoctrine()->getManager();

        $paricevuti = array("nomebundle" => $nomebundle, "nometabella" => $request->get("nometabella"), "container" => $container, "request" => $request);

        if ($request->get("parametritesta")) {
            $parametritesta = get_object_vars(json_decode($request->get("parametritesta")));
            $parametritesta["container"] = $container;
            $parametritesta["doctrine"] = $em;
            $parametritesta["request"] = $request;
            $parametritesta["output"] = "stampa";
        }

        $testatagriglia = griglia::testataPerGriglia($request->get("parametritesta") ? $parametritesta : $paricevuti);
        if ($request->get("titolo")) {
            $testatagriglia["titolo"] = $request->get("titolo");
        }

        if ($request->get("parametrigriglia")) {
            $parametrigriglia = get_object_vars(json_decode($request->get("parametrigriglia")));
            $parametrigriglia["container"] = $container;
            $parametrigriglia["doctrine"] = $em;
            $parametrigriglia["request"] = $request;
            $parametrigriglia["output"] = "stampa";
        }

        $corpogriglia = griglia::datiPerGriglia($request->get("parametrigriglia") ? $parametrigriglia : $paricevuti);

        $parametri = array("request" => $request, "testata" => $testatagriglia, "griglia" => $corpogriglia);

        $fileexcel = $xls->esportaexcel($parametri);
        $response = new Response();

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . basename($fileexcel) . '"');

        $response->setContent(file_get_contents($fileexcel));
        return $response;
    }

    public function getNamespace() {
        return self::$namespace;
    }

    public function getBundle() {
        return self::$bundle;
    }

    public function getController() {
        return self::$controller;
    }

    public function getAction() {
        return self::$action;
    }

}

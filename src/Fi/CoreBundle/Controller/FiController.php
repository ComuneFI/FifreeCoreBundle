<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FiController extends Controller
{

    public static $namespace;
    public static $bundle;
    public static $controller;
    public static $action;
    public static $parametrigriglia;

    protected function setup(Request $request)
    {
        $matches = array();
        $controllo = new \ReflectionClass(get_class($this));

        preg_match('/(.*)\\\(.*)Bundle\\\Controller\\\(.*)Controller/', $controllo->getName(), $matches);

        self::$namespace = $matches[1];
        self::$bundle = $matches[2];
        self::$controller = $matches[3];
        self::$action = substr($request->attributes->get('_controller'), strrpos($request->attributes->get('_controller'), ':') + 1);
    }

    protected function setParametriGriglia($prepar = array())
    {
        self::setup($prepar['request']);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $escludi = array();

        $paricevuti = array('container' => $this->container, 'nomebundle' => $nomebundle, 'nometabella' => $controller, 'escludere' => $escludi);

        if ($prepar) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }

    /**
     * Lists all tables entities.
     */
    public function indexAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        self::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $gestionepermessi = new GestionepermessiController();
        $gestionepermessi->setContainer($this->container);

        $canRead = ($gestionepermessi->leggereAction(array('modulo' => $controller)) ? 1 : 0);
        $idpassato = $request->get('id');

        $nomebundle = $namespace . $bundle . 'Bundle';

        $repotabelle = $this->container->get('OpzioniTabella_repository');

        $paricevuti = array('nomebundle' => $nomebundle, 'nometabella' => $controller, 'container' => $container);

        $testatagriglia = Griglia::testataPerGriglia($paricevuti);

        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 1;
        $testatagriglia['overlayopen'] = 1;

        $testatagriglia['parametritesta'] = json_encode($paricevuti);

        $this->setParametriGriglia(array('request' => $request));
        $testatagriglia['parametrigriglia'] = json_encode(self::$parametrigriglia);

        $testata = $repotabelle->editTestataFormTabelle($testatagriglia, $controller, $container);

        return $this->render(
            $nomebundle . ':' . $controller . ':index.html.twig',
            array(
                    'nomecontroller' => $controller,
                    'testata' => $testata,
                    'canread' => $canRead,
                    'idpassato' => $idpassato
                        )
        );
    }

    public function grigliaAction(Request $request)
    {
        $this->setParametriGriglia(array('request' => $request));
        $paricevuti = self::$parametrigriglia;

        return new Response(Griglia::datiPerGriglia($paricevuti));
    }

    /**
     * Creates a new table entity.
     */
    public function createAction(Request $request)
    {
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $classbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Entity\\' . $controller;
        $formbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Form\\' . $controller;

        $entity = new $classbundle();
        $formType = $formbundle . 'Type';

        $form = $this->createForm($formType, $entity);

        $form->submit($request->request->get($form->getName()));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $continua = $request->get('continua');
            if ($continua == 0) {
                return new Response('OK');
            } else {
                return $this->redirect($this->generateUrl($controller . '_edit', array('id' => $entity->getId())));
            }
        }

        return $this->render(
            $nomebundle . ':' . $controller . ':new.html.twig',
            array(
                    'nomecontroller' => $controller,
                    'entity' => $entity,
                    'form' => $form->createView(),
                        )
        );
    }

    /**
     * Displays a form to create a new table entity.
     */
    public function newAction(Request $request)
    {
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $classbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Entity\\' . $controller;
        $formbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Form\\' . $controller;
        $formType = $formbundle . 'Type';

        $entity = new $classbundle();

        $form = $this->createForm(
            $formType,
            $entity,
            array('attr' => array(
                'id' => 'formdati' . $controller,
                ),
                'action' => $this->generateUrl($controller . '_create'),
                )
        );

        return $this->render(
            $nomebundle . ':' . $controller . ':new.html.twig',
            array(
                    'nomecontroller' => $controller,
                    'entity' => $entity,
                    'form' => $form->createView(),
                        )
        );
    }

    protected function elencoModifiche($nomebundle, $controller, $id)
    {
        $controllerStorico = "Storicomodifiche";
        $em = $this->getDoctrine()->getManager();
        $risultato = $em->getRepository('FiCoreBundle:' . $controllerStorico)->findBy(
            array(
                    "nometabella" => $controller,
                    "idtabella" => $id
                )
        );

        return $risultato;
    }

    /**
     * Displays a form to edit an existing table entity.
     */
    public function editAction(Request $request, $id)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $allegati = ($request->get('allegati') == 1 ? 1 : 0);


        $nomebundle = $namespace . $bundle . 'Bundle';
        $formbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Form\\' . $controller;
        $formType = $formbundle . 'Type';

        $elencomodifiche = $this->elencoModifiche($nomebundle, $controller, $id);

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($nomebundle . ':' . $controller)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $controller . ' entity.');
        }

        $editForm = $this->createForm(
            $formType,
            $entity,
            array('attr' => array(
                'id' => 'formdati' . $controller,
                ),
                'action' => $this->generateUrl($controller . '_update', array('id' => $entity->getId())),
                )
        );

        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            $nomebundle . ':' . $controller . ':edit.html.twig',
            array(
                    'entity' => $entity,
                    'nomecontroller' => $controller,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'elencomodifiche' => $elencomodifiche,
                    'allegati' => $allegati,
                        )
        );
    }

    /**
     * Edits an existing table entity.
     */
    public function updateAction(Request $request, $id)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        self::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $formbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Form\\' . $controller;
        $formType = $formbundle . 'Type';

        $repoStorico = $this->container->get('Storicomodifiche_repository');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($nomebundle . ':' . $controller)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $controller . ' entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $editForm = $this->createForm($formType, $entity);

        $editForm->submit($request->request->get($editForm->getName()));

        if ($editForm->isValid()) {
            $originalData = $em->getUnitOfWork()->getOriginalEntityData($entity);

            $em->persist($entity);
            $em->flush();

            $newData = $em->getUnitOfWork()->getOriginalEntityData($entity);
            $changes = $repoStorico->isRecordChanged($nomebundle, $controller, $originalData, $newData);

            if ($changes) {
                $repoStorico->saveHistory($controller, $changes, $id, $this->getUser());
            }

            $continua = $request->get('continua');
            if ($continua == 0) {
                return new Response('OK');
            } else {
                return $this->redirect($this->generateUrl($controller . '_edit', array('id' => $id)));
            }
        }

        return $this->render(
            $nomebundle . ':' . $controller . ':edit.html.twig',
            array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'nomecontroller' => $controller,
                        )
        );
    }

    /**
     * Edits an existing table entity.
     */
    public function aggiornaAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';

        $id = $this->get('request')->request->get('id');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($nomebundle . ':' . $controller)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $controller . ' entity.');
        }

        throw $this->createNotFoundException("Implementare a seconda dell'esigenza 'aggiornaAction' del controller "
                . $nomebundle
                . '/'
                . $controller);
    }

    /**
     * Deletes a table entity.
     */
    public function deleteAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';

        //if (!$request->isXmlHttpRequest()) {
        //    $request->checkCSRFProtection();
        //}
        try {
            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            $ids = explode(',', $request->get('id'));
            $qb->delete($nomebundle . ':' . $controller, 'u')
                    ->andWhere('u.id IN (:ids)')
                    ->setParameter('ids', $ids);

            $query = $qb->getQuery();
            $query->execute();
        } catch (\Exception $e) {
            $response = new Response();
            $response->setStatusCode('200');

            return new Response('404');
        }

        return new Response('OK');
    }

    /**
     * Creates a form to delete a table entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', get_class(new \Symfony\Component\Form\Extension\Core\Type\HiddenType()))
                        ->getForm();
    }

    public function prepareOutput($request)
    {
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . 'Bundle';

        $em = $this->getDoctrine()->getManager();

        $paricevuti = array(
            'nomebundle' => $nomebundle,
            'nometabella' => $request->get('nometabella'),
            'container' => $container,
            'request' => $request,
        );

        $parametripertestatagriglia = $this->getParametersTestataPerGriglia($request, $container, $em, $paricevuti);

        $testatagriglia = Griglia::testataPerGriglia($parametripertestatagriglia);

        if ($request->get('titolo')) {
            $testatagriglia['titolo'] = $request->get('titolo');
        }
        $parametridatipergriglia = $this->getParametersDatiPerGriglia($request, $container, $em, $paricevuti);
        $corpogriglia = Griglia::datiPerGriglia($parametridatipergriglia);

        $parametri = array('request' => $request, 'testata' => $testatagriglia, 'griglia' => $corpogriglia);
        return $parametri;
    }

    public function stampatabellaAction(Request $request)
    {
        self::setup($request);
        $pdf = new StampatabellaController($this->container);

        $parametri = $this->prepareOutput($request);

        $pdf->stampa($parametri);

        return new Response('OK');
    }

    public function esportaexcelAction(Request $request)
    {
        self::setup($request);
        $xls = new StampatabellaController($this->container);

        $parametri = $this->prepareOutput($request);

        $fileexcel = $xls->esportaexcel($parametri);
        $response = new Response();

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . basename($fileexcel) . '"');

        $response->setContent(file_get_contents($fileexcel));

        return $response;
    }

    public function importaexcelAction(Request $request)
    {
        self::setup($request);
        $return = "OK";
        try {
            $em = $this->getDoctrine()->getManager();
            $file = $request->files->get('file');
            $tablenamefile = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file->getClientOriginalName());
            //$namespace = $this->getNamespace();
            $parametri = json_decode($request->get("parametrigriglia"));
            $bundle = $parametri->nomebundle;
            $controller = $parametri->nometabella;
            //$nomebundle = $namespace . $bundle . 'Bundle';
            $repo = $em->getRepository($bundle . ":" . $tablenamefile);
            $className = $repo->getClassName();

            $classentitypath = "\\" . $className;
            if (strtolower($controller) != strtolower($tablenamefile)) {
                $response = new Response("Si sta cercando di caricare i dati di " . $tablenamefile . " in " . $controller);
                return $response;
            }

            if (is_a($repo, "Doctrine\ORM\EntityRepository")) {
                $objPHPExcel = \PHPExcel_IOFactory::load($file);
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    //$worksheetTitle = $worksheet->getTitle();
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
                    $colonne = array();
                    for ($index = 0; $index < $highestColumnIndex; $index++) {
                        $colonne[] = strtolower($worksheet->getCellByColumnAndRow($index, 1)->getValue());
                    }
                    for ($rows = 2; $rows < $highestRow + 1; $rows++) {
                        $newentity = new $classentitypath();
                        for ($cols = 0; $cols < $highestColumnIndex; $cols++) {
                            if ($colonne[$cols] != "id") {
                                $valore = $worksheet->getCellByColumnAndRow($cols, $rows)->getValue();
                                $fieldset = "set" . ucfirst($colonne[$cols]);
                                $newentity->$fieldset($valore);
                            }
                        }
                        $em->persist($newentity);
                    }
                    $em->flush();
                }
            } else {
                $response = new Response($return);
                return $response;
            }
        } catch (\Exception $exc) {
            $response = new Response($exc->getMessage());
            return $response;
        }

        $response = new Response($return);
        return $response;
    }

    private function getParametersTestataPerGriglia($request, $container, $em, $paricevuti)
    {
        if ($request->get('parametritesta')) {
            $jsonparms = json_decode($request->get('parametritesta'));
            $parametritesta = get_object_vars($jsonparms);
            $parametritesta['container'] = $container;
            $parametritesta['doctrine'] = $em;
            $parametritesta['request'] = $request;
            $parametritesta['output'] = 'stampa';
        }

        return $request->get('parametritesta') ? $parametritesta : $paricevuti;
    }

    private function getParametersDatiPerGriglia($request, $container, $em, $paricevuti)
    {
        if ($request->get('parametrigriglia')) {
            $jsonparms = json_decode($request->get('parametrigriglia'));
            $parametrigriglia = get_object_vars($jsonparms);
            $parametrigriglia['container'] = $container;
            $parametrigriglia['doctrine'] = $em;
            $parametrigriglia['request'] = $request;
            $parametrigriglia['output'] = 'stampa';
        }

        return $request->get('parametrigriglia') ? $parametrigriglia : $paricevuti;
    }

    protected function getNamespace()
    {
        return self::$namespace;
    }

    protected function getBundle()
    {
        return self::$bundle;
    }

    protected function getController()
    {
        return self::$controller;
    }

    protected function getAction()
    {
        return self::$action;
    }
}

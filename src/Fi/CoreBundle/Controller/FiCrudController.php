<?php

namespace Fi\CoreBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FiCrudController extends Controller
{

    public static $namespace;
    public static $bundle;
    public static $controller;
    public static $action;
    public static $parametrigriglia;
    public static $canRead;
    public static $canDelete;
    public static $canCreate;
    public static $canUpdate;

    protected function setup(Request $request)
    {
        $matches = array();
        $controllo = new ReflectionClass(get_class($this));

        preg_match('/(.*)\\\(.*)Bundle\\\Controller\\\(.*)Controller/', $controllo->name, $matches);
        if (count($matches) == 0) {
            preg_match('/(.*)(.*)\\\Controller\\\(.*)Controller/', $controllo->name, $matches);
        }
        self::$namespace = $matches[1];
        self::$bundle = $matches[2];
        self::$controller = $matches[3];
        self::$action = substr($request->attributes->get('_controller'), strrpos($request->attributes->get('_controller'), ':') + 1);

        $gestionepermessi = $this->get('ficorebundle.gestionepermessi');
        self::$canRead = ($gestionepermessi->leggere(array('modulo' => self::$controller)) ? 1 : 0);
        self::$canDelete = ($gestionepermessi->cancellare(array('modulo' => self::$controller)) ? 1 : 0);
        self::$canCreate = ($gestionepermessi->creare(array('modulo' => self::$controller)) ? 1 : 0);
        self::$canUpdate = ($gestionepermessi->aggiornare(array('modulo' => self::$controller)) ? 1 : 0);
    }

    /**
     * Lists all tables entities.
     */
    public function indexAction(Request $request)
    {
        /* @var $em EntityManager */
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        if (!self::$canRead) {
            throw new AccessDeniedException("Non si hanno i permessi per visualizzare questo contenuto");
        }

        $container = $this->container;

        $idpassato = $request->get('id');

        $nomebundle = $namespace . $bundle . 'Bundle';

        $repotabelle = $this->get('OpzioniTabella_repository');

        $paricevuti = array('nomebundle' => $nomebundle, 'nometabella' => $controller, 'container' => $container);

        $griglia = $this->get("ficorebundle.griglia");
        $testatagriglia = $griglia->testataPerGriglia($paricevuti);

        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 1;
        $testatagriglia['overlayopen'] = 1;
        $testatagriglia['showadd'] = self::$canCreate;
        $testatagriglia['showedit'] = self::$canUpdate;
        $testatagriglia['showdel'] = self::$canDelete;
        $testatagriglia["filterToolbar_searchOnEnter"] = true;
        $testatagriglia["filterToolbar_searchOperators"] = true;

        $testatagriglia['parametritesta'] = json_encode($paricevuti);

        $this->setParametriGriglia(array('request' => $request));
        $testatagriglia['parametrigriglia'] = json_encode(self::$parametrigriglia);

        $template = $nomebundle . ':' . $controller . ':index.html.twig';
        if (!$this->get('templating')->exists($template)) {
            $template = $controller . '/index.html.twig';
        }

        $testata = $repotabelle->editTestataFormTabelle($testatagriglia, $controller, $container);
        return $this->render(
            $template,
            array(
                    'nomecontroller' => $controller,
                    'testata' => $testata,
                    'canread' => self::$canRead,
                    'idpassato' => $idpassato,
                        )
        );
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

        if (!self::$canCreate) {
            throw new AccessDeniedException("Non si hanno i permessi per creare questo contenuto");
        }

        $nomebundle = $namespace . $bundle . 'Bundle';
        $classbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Entity\\' . $controller;
        $formbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Form\\' . $controller;

        if (!class_exists($classbundle)) {
            $nomebundle = $namespace . $bundle . 'Bundle';
            $classbundle = $namespace . '\\Entity\\' . $controller;
            $formbundle = $namespace . '\\Form\\' . $controller;
            $template = $controller . '/new.html.twig';
        } else {
            $template = $nomebundle . ':' . $controller . ':new.html.twig';
        }

        $entity = new $classbundle();
        $formType = $formbundle . 'Type';

        $form = $this->createForm(
            $formType,
            $entity,
            array('attr' => array(
                'id' => 'formdati' . $controller,
                ),
                'action' => $this->generateUrl($controller . '_create'),
                )
        );

        $form->submit($request->request->get($form->getName()));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $continua = (int) $request->get('continua');
            if ($continua === 0) {
                return new Response('OK');
            } else {
                return $this->redirect($this->generateUrl($controller . '_edit', array('id' => $entity->getId())));
            }
        }

        return $this->render(
            $template,
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

        if (!self::$canCreate) {
            throw new AccessDeniedException("Non si hanno i permessi per creare questo contenuto");
        }

        $nomebundle = $namespace . $bundle . 'Bundle';
        $classbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Entity\\' . $controller;
        $formbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Form\\' . $controller;
        $formType = $formbundle . 'Type';
        if (!class_exists($classbundle)) {
            $nomebundle = $namespace . $bundle . 'Bundle';
            $classbundle = $namespace . '\\Entity\\' . $controller;
            $formbundle = $namespace . '\\Form\\' . $controller;
            $formType = $formbundle . 'Type';
            $template = $controller . '/new.html.twig';
        } else {
            $template = $nomebundle . ':' . $controller . ':new.html.twig';
        }
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
            $template,
            array(
                    'nomecontroller' => $controller,
                    'entity' => $entity,
                    'form' => $form->createView(),
                        )
        );
    }

    /**
     * Displays a form to edit an existing table entity.
     */
    public function editAction(Request $request, $id)
    {
        /* @var $em EntityManager */
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        if (!self::$canUpdate) {
            throw new AccessDeniedException("Non si hanno i permessi per modificare questo contenuto");
        }

        $nomebundle = $namespace . $bundle . 'Bundle';
        $formbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Form\\' . $controller;
        $formType = $formbundle . 'Type';
        if (!class_exists($formType)) {
            $nomebundle = $namespace . $bundle . 'Bundle';
            $formbundle = $namespace . '\\Form\\' . $controller;
            $formType = $formbundle . 'Type';
            $template = $controller . '/edit.html.twig';
        } else {
            $template = $nomebundle . ':' . $controller . ':edit.html.twig';
        }

        $elencomodifiche = $this->elencoModifiche($controller, $id);

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
            $template,
            array(
                    'entity' => $entity,
                    'nomecontroller' => $controller,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'elencomodifiche' => $elencomodifiche,
                        )
        );
    }

    /**
     * Edits an existing table entity.
     */
    public function updateAction(Request $request, $id)
    {
        /* @var $em EntityManager */
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        if (!self::$canUpdate) {
            throw new AccessDeniedException("Non si hanno i permessi per aggiornare questo contenuto");
        }

        $nomebundle = $namespace . $bundle . 'Bundle';
        $formbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Form\\' . $controller;
        $formType = $formbundle . 'Type';

        if (!class_exists($formType)) {
            $nomebundle = $namespace . $bundle . 'Bundle';
            $formbundle = $namespace . '\\Form\\' . $controller;
            $formType = $formbundle . 'Type';
            $template = $controller . '/edit.html.twig';
        } else {
            $template = $nomebundle . ':' . $controller . ':edit.html.twig';
        }

        $repoStorico = $this->container->get('Storicomodifiche_repository');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($nomebundle . ':' . $controller)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $controller . ' entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $editForm = $this->createForm(
            $formType,
            $entity,
            array('attr' => array(
                'id' => 'formdati' . $controller,
                ),
                'action' => $this->generateUrl($controller . '_update', array('id' => $entity->getId())),
                )
        );

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

            $continua = (int) $request->get('continua');
            if ($continua === 0) {
                return new Response('OK');
            } else {
                return $this->redirect($this->generateUrl($controller . '_edit', array('id' => $id)));
            }
        }

        return $this->render(
            $template,
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
        /* @var $em EntityManager */
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        if (!self::$canUpdate) {
            throw new AccessDeniedException("Non si hanno i permessi per aggiornare questo contenuto");
        }

        $nomebundle = $namespace . $bundle . 'Bundle';

        $id = $request->get('id');

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
        /* @var $em EntityManager */
        $this->setup($request);
        if (!self::$canDelete) {
            throw new AccessDeniedException("Non si hanno i permessi per aggiornare questo contenuto");
        }
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';

        try {
            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            $ids = explode(',', $request->get('id'));
            $qb->delete($nomebundle . ':' . $controller, 'u')
                    ->andWhere('u.id IN (:ids)')
                    ->setParameter('ids', $ids);

            $query = $qb->getQuery();
            $query->execute();
        } catch (ForeignKeyConstraintViolationException $e) {
            return new Response('404');
        } catch (\Exception $e) {
            $response = new Response($e->getMessage());
            $response->setStatusCode('200');
            return $response;
        }

        return new Response('OK');
    }

    /**
     * Creates a form to delete a table entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', get_class(new HiddenType()))
                        ->getForm();
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

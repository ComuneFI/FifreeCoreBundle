<?php

/* Qui Bundle */
//namespace Fi\DemoBundle\Controller;

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * OpzioniTabella controller.
 */
class OpzioniTabellaController extends FiCoreController
{

    /**
     * Lists all opzioniTabella entities.
     */
    public function indexAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . 'Bundle';

        $dettaglij = array(
            'descrizione' => array(
                array(
                    'nomecampo' => 'descrizione',
                    'lunghezza' => '400',
                    'descrizione' => 'Descrizione',
                    'tipo' => 'text',),),
            'parametro' => array(
                array(
                    'nomecampo' => 'parametro',
                    'lunghezza' => '300',
                    'descrizione' => 'Parametro',
                    'tipo' => 'text',),),
            'valore' => array(
                array(
                    'nomecampo' => 'valore',
                    'lunghezza' => '300',
                    'descrizione' => 'Valore',
                    'tipo' => 'text',),),
            'tabelle_id' => array(
                array(
                    'nomecampo' => 'tabelle.nometabella',
                    'lunghezza' => '400',
                    'descrizione' => 'Tabella',
                    'tipo' => 'text',),
            ),
        );

        $escludi = array();
        $paricevuti = array(
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'dettaglij' => $dettaglij,
            'escludere' => $escludi,
            'container' => $container,
        );

        $testatagriglia = Griglia::testataPerGriglia($paricevuti);

        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 1;
        $testatagriglia['showadd'] = 1;
        $testatagriglia['showedit'] = 1;
        $testatagriglia['showdel'] = 1;
        $testatagriglia['editinline'] = 0;

        $testatagriglia['parametritesta'] = json_encode($paricevuti);
        $this->setParametriGriglia(array('request' => $request));
        $testatagriglia['parametrigriglia'] = json_encode(self::$parametrigriglia);

        $testata = json_encode($testatagriglia);
        $twigparms = array(
            'nomecontroller' => $controller,
            'testata' => $testata,
        );

        return $this->render($nomebundle . ':' . $controller . ':index.html.twig', $twigparms);
    }

    public function setParametriGriglia($prepar = array())
    {
        $this->setup($prepar['request']);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $escludi = array();
        $tabellej = array();
        $tabellej['tabelle_id'] = array('tabella' => 'tabelle', 'campi' => array('nometabella'));

        $paricevuti = array(
            'container' => $this->container,
            'nomebundle' => $nomebundle,
            'tabellej' => $tabellej,
            'nometabella' => $controller,
            'escludere' => $escludi,);

        if (!empty($prepar)) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
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
            $em->getConfiguration()->getResultCacheImpl()->delete($controller);

            $continua = (int) $request->get('continua');
            if ($continua === 0) {
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
     * Edits an existing table entity.
     */
    public function updateAction(Request $request, $id)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
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
            $em->getConfiguration()->getResultCacheImpl()->delete($controller);

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
     * Deletes a table entity.
     */
    public function deleteAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
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
            $em->getConfiguration()->getResultCacheImpl()->delete($controller);
        } catch (\Exception $e) {
            $response = new Response();
            $response->setStatusCode('200');

            return new Response('404');
        }

        return new Response('OK');
    }
}

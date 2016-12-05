<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allegati controller.
 */
class AllegatiController extends FiCoreController
{

    /**
     * Edits an existing table entity.
     */
    /* @var $em \Doctrine\ORM\EntityManager */
    public function updateAction(Request $request, $id)
    {
        self::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $formbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Form\\' . $controller;
        $formType = $formbundle . 'Type';

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($nomebundle . ':' . $controller)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $controller . ' entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm($formType, $entity);
        $editForm->submit($request->request->get($editForm->getName()));

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl($controller . '_editiframe', array('id' => $id)));
        }
        $twigparms = array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'nomecontroller' => $controller,
        );

        return $this->render($nomebundle . ':' . $controller . ':editiframe.html.twig', $twigparms);
    }

    /**
     * Displays a form to edit an existing table entity.
     */
    /* @var $em \Doctrine\ORM\EntityManager */
    public function editAction(Request $request, $id)
    {
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $allegati = ($request->get('allegati') == 1 ? 1 : 0);

        $nomebundle = $namespace . $bundle . 'Bundle';
        $formbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Form\\' . $controller;
        $formType = $formbundle . 'Type';

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
        $twigparms = array(
            'entity' => $entity,
            'nomecontroller' => $controller,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'allegati' => $allegati,
            'id' => $entity->getId(),
        );

        return $this->render($nomebundle . ':' . $controller . ':edit.html.twig', $twigparms);
    }

    /**
     * Displays a form to edit an existing table entity.
     */
    /* @var $em \Doctrine\ORM\EntityManager */
    public function editiframeAction(Request $request, $id)
    {
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $allegati = ($request->get('allegati') == 1 ? 1 : 0);

        $nomebundle = $namespace . $bundle . 'Bundle';
        $formbundle = $namespace . '\\' . $bundle . 'Bundle' . '\\Form\\' . $controller;
        $formType = $formbundle . 'Type';

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($nomebundle . ':' . $controller)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $controller . ' entity.');
        }
        $formparms = array(
            'attr' => array(
                'id' => 'formdati' . $controller,
                'action' => $this->generateUrl($controller . '_update', array('id' => $entity->getId())),
            ),
        );
        $editForm = $this->createForm($formType, $entity, $formparms);
        $deleteForm = $this->createDeleteForm($id);
        $twigparms = array(
            'entity' => $entity,
            'nomecontroller' => $controller,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'allegati' => $allegati,
            'id' => $entity->getId(),
        );

        return $this->render($nomebundle . ':' . $controller . ':editiframe.html.twig', $twigparms);
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
        $form->handleRequest($request);
        //var_dump($request);exit;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl($controller . '_editiframe', array('id' => $entity->getId())));
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
                    'nometabella' => $request->get('nometabella'),
                    'indicetabella' => $request->get('indicetabella'),
                        )
        );
    }

    /**
     * Displays a form to create a new table entity.
     */
    public function newiframeAction(Request $request)
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

        $entity->setNometabella($request->get('nometabella'));
        $entity->setIndicetabella($request->get('indicetabella'));

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
            $nomebundle . ':' . $controller . ':newiframe.html.twig',
            array(
                    'nomecontroller' => $controller,
                    'entity' => $entity,
                    'form' => $form->createView(),
                        )
        );
    }

    public function popupAction(Request $request, $nometabella, $id)
    {

        //    $entities = $em->getRepository($nomebundle . ':' . $controller)->findBy(
        //            array('nometabella' => $nometabella, 'indicetabella' => $id), array('allegato' => 'ASC')
        //    );

        self::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $gestionepermessi = new GestionepermessiController();
        $gestionepermessi->setContainer($this->container);
        $gestionepermessi->utentecorrenteAction();
        $canRead = ($gestionepermessi->leggereAction(array('modulo' => $controller)) ? 1 : 0);

        $nomebundle = $namespace . $bundle . 'Bundle';

        $em = $container->get('doctrine')->getManager();
        //$entities = $em->getRepository($nomebundle . ':' . $controller)->findAll();

        $paricevuti = array(
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'container' => $container,
            'escludere' => array('nometabella', 'indicetabella', 'allegatofile'),
        );

        $testatagriglia = Griglia::testataPerGriglia($paricevuti);

        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 0;
        $testatagriglia['showprint'] = 0;
        $testatagriglia['nomelist'] = '#listallegati';
        $testatagriglia['nomepager'] = '#pagerallegati';
        $testatagriglia['nascondicaption'] = 1;
        $testatagriglia['larghezzagriglia'] = 485;
        $testatagriglia['altezzagriglia'] = 200;
        $testatagriglia['ridimensionabile'] = 0;

        $testatagriglia['parametriaggiuntivi_new'] = array('nometabella' => $nometabella, 'indicetabella' => $id);

        $testatagriglia['datipost'] = array('nometabella' => $nometabella, 'indicetabella' => $id);

        // "nometabella" => $nometabella, "indicetabella" => $id

        $testatagriglia['parametritesta'] = json_encode($paricevuti);

        $this->setParametriGriglia(array('request' => $request));
        $testatagriglia['parametrigriglia'] = json_encode(self::$parametrigriglia);

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('a'));
        $qb->from('FiCoreBundle:OpzioniTabella', 'a');
        $qb->leftJoin('a.tabelle', 't');
        $qb->where('t.nometabella = :tabella');
        $qb->andWhere("t.nomecampo is null or t.nomecampo = ''");
        $qb->setParameter('tabella', $controller);
        $opzioni = $qb->getQuery()->getResult();
        foreach ($opzioni as $opzione) {
            $testatagriglia[$opzione->getParametro()] = $opzione->getValore();
        }

        $testata = json_encode($testatagriglia);

        return $this->render(
            $nomebundle . ':' . $controller . ':popup.html.twig',
            array(
                    //'entities' => $entities,
                    'nomecontroller' => $controller,
                    'testata' => $testata,
                    'canread' => $canRead,
                    'nometabella' => $nometabella,
                    'id' => $id,
                        )
        );
    }

    public function grigliaAction(Request $request)
    {
        $nometabella = $request->get('nometabella');
        $indicetabella = $request->get('indicetabella');

        $prepar = array('request' => $request,
            'precondizioniAvanzate' => array(
                array(
                    'nometabella' => 'allegati',
                    'nomecampo' => 'nometabella',
                    'operatore' => '=',
                    'valorecampo' => $nometabella,
                ),
                array(
                    'nometabella' => 'allegati',
                    'nomecampo' => 'indicetabella',
                    'operatore' => '=',
                    'valorecampo' => $indicetabella,
                ),),
        );
        //$prepar = array("request" => $request, "precondizioni" => array("indicetabella" => $indicetabella));
        $this->setParametriGriglia($prepar);
        $paricevuti = self::$parametrigriglia;

        return new Response(Griglia::datiPerGriglia($paricevuti));
    }

    public function setParametriGriglia($prepar = array())
    {
        self::setup($prepar['request']);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $escludi = array('nometabella', 'indicetabella');

        $paricevuti = array(
            'container' => $this->container,
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'escludere' => $escludi,);

        if ($prepar) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }
        self::$parametrigriglia = $paricevuti;
    }
}

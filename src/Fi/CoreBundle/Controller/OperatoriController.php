<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Fi\CoreBundle\Entity\Operatori;

/**
 * Operatori controller.
 */
class OperatoriController extends FiCoreController
{

    /**
     * Lists all Ffprincipale entities.
     */
    public function indexAction(Request $request)
    {
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';

        $em = $this->getDoctrine()->getManager();
        $container = $this->container;
        $entities = $em->getRepository($nomebundle . ':' . $controller)->findAll();

        $dettaglij = array('ruoli_id' => array(
                array('nomecampo' => 'ruoli.ruolo',
                    'lunghezza' => '200',
                    'descrizione' => 'Ruolo',
                    'tipo' => 'text',),
        ));

        $paricevuti = array(
            'doctrine' => $em,
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'dettaglij' => $dettaglij,
            'container' => $container,);

        $testatagriglia = Griglia::testataPerGriglia($paricevuti);

        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 1;

        $testata = json_encode($testatagriglia);
        $twigparms = array(
            'entities' => $entities,
            'nomecontroller' => $controller,
            'testata' => $testata,);

        return $this->render($nomebundle . ':' . $controller . ':index.html.twig', $twigparms);
    }

    public function setParametriGriglia($prepar = array())
    {
        self::setup($prepar['request']);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $escludi = array();
        $tabellej['ruoli_id'] = array('tabella' => 'ruoli', 'campi' => array('ruolo'));

        $paricevuti = array(
            'container' => $this->container,
            'nomebundle' => $nomebundle,
            'tabellej' => $tabellej,
            'nometabella' => $controller,
            'escludere' => $escludi,);

        if ($prepar) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }
}

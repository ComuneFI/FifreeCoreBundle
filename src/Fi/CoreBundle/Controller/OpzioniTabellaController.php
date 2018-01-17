<?php

/* Qui Bundle */
//namespace Fi\DemoBundle\Controller;

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

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
        $tabellej['tabelle_id'] = array('tabella' => 'tabelle', 'campi' => array('nometabella'));

        $paricevuti = array(
            'container' => $this->container,
            'nomebundle' => $nomebundle,
            'tabellej' => $tabellej,
            'nometabella' => $controller,
            'escludere' => $escludi,);

        if (! empty($prepar)) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }
}

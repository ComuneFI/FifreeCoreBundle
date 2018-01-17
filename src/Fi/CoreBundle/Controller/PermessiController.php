<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Fi\CoreBundle\Entity\Permessi;

/**
 * Permessi controller.
 */
class PermessiController extends FiCoreController
{

    /**
     * Lists all Ffprincipale entities.
     */
    public function indexAction(Request $request)
    {
        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . 'Bundle';

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository($nomebundle . ':' . $controller)->findAll();

        $dettaglij = array(
            'operatori_id' => array(
                array('nomecampo' => 'operatori.username',
                    'lunghezza' => '200',
                    'descrizione' => 'Username',
                    'tipo' => 'text',),
                array('nomecampo' => 'operatori.operatore',
                    'lunghezza' => '200',
                    'descrizione' => 'Operatore',
                    'tipo' => 'text',),
            ),
            'ruoli_id' => array(
                array('nomecampo' => 'ruoli.ruolo',
                    'lunghezza' => '200',
                    'descrizione' => 'Ruolo',
                    'tipo' => 'text',),
            ),
        );

        $paricevuti = array(
            'doctrine' => $em,
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'dettaglij' => $dettaglij,
            'container' => $container,);

        $testatagriglia = Griglia::testataPerGriglia($paricevuti);

        $testata = json_encode($testatagriglia);
        $twigparms = array(
            'entities' => $entities,
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
        $tabellej['operatori_id'] = array('tabella' => 'operatori', 'campi' => array('username', 'operatore'));
        $tabellej['ruoli_id'] = array('tabella' => 'ruoli', 'campi' => array('ruolo'));

        $paricevuti = array(
            'container' => $this->container,
            'nomebundle' => $nomebundle,
            'tabellej' => $tabellej,
            'nometabella' => $controller,
            'escludere' => $escludi,
        );

        if (! empty($expr)) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }
}

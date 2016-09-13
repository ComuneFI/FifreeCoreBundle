<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Ffsecondaria controller.
 */
class FfsecondariaController extends FiController
{
    public function indexAction(Request $request)
    {
        parent::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $nomebundle = $namespace.$bundle.'Bundle';

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository($nomebundle.':'.$controller)->findAll();

        $dettaglij = array(
            'descsec' => array(array('nomecampo' => 'descsec', 'lunghezza' => '400', 'descrizione' => 'Descrizione tabella secondaria', 'tipo' => 'text')),
            'ffprincipale_id' => array(array('nomecampo' => 'ffprincipale.descrizione', 'lunghezza' => '400', 'descrizione' => 'Descrizione record principale', 'tipo' => 'text')),
                /* ,
                  array("nomecampo" => "ffprincipale.id", "lunghezza" => "40", "descrizione" => "IdP", "tipo" => "integer") */
        );
        $escludi = array();
        $paricevuti = array('nomebundle' => $nomebundle, 'nometabella' => $controller, 'dettaglij' => $dettaglij, 'escludere' => $escludi, 'container' => $container);

        $testatagriglia = Griglia::testataPerGriglia($paricevuti);

        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 1;
        $testatagriglia['showadd'] = 1;
        $testatagriglia['showedit'] = 1;
        $testatagriglia['showdel'] = 1;

        //$testatagriglia["filterToolbar_stringResult"] = false;
        //$testatagriglia["filterToolbar_searchOnEnter"] = true;
        //$testatagriglia["filterToolbar_searchOperators"] = false;
        //$testatagriglia["filterToolbar_clearSearch"] = false;

        $testatagriglia['parametritesta'] = json_encode($paricevuti);
        $this->setParametriGriglia(array('request' => $request));
        $testatagriglia['parametrigriglia'] = json_encode(self::$parametrigriglia);

        $testata = json_encode($testatagriglia);

        return $this->render(
            $nomebundle.':'.$controller.':index.html.twig', array(
                    'entities' => $entities,
                    'nomecontroller' => $controller,
                    'testata' => $testata,
            )
        );
    }

    public function setParametriGriglia($prepar = array())
    {
        self::setup($prepar['request']);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace.$bundle.'Bundle';
        $escludi = array();
        $tabellej['ffprincipale_id'] = array('tabella' => 'ffprincipale', 'campi' => array('descrizione'));

        $paricevuti = array('container' => $this->container, 'nomebundle' => $nomebundle, 'tabellej' => $tabellej, 'nometabella' => $controller, 'escludere' => $escludi);

        if ($prepar) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }
}

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

        $dettaglij = array(
            'descsec' => array(
                array('nomecampo' => 'descsec',
                    'lunghezza' => '400',
                    'descrizione' => 'Descrizione tabella secondaria',
                    'tipo' => 'text', ), ),
            'ffprincipale_id' => array(
                array('nomecampo' => 'ffprincipale.descrizione',
                    'lunghezza' => '400',
                    'descrizione' => 'Descrizione record principale',
                    'tipo' => 'text', ),
            ),
        );
        $escludi = array('nota');

        $campiextra = array(
            array('nomecampo' => 'lunghezzanota', 'lunghezza' => '80', 'descrizione' => 'Lunghezza Nota', 'tipo' => 'integer'),
            array('nomecampo' => 'attivoToString', 'lunghezza' => '80', 'descrizione' => 'Attivo string', 'tipo' => 'text'),
        );

        $paricevuti = array(
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'dettaglij' => $dettaglij,
            'campiextra' => $campiextra,
            'escludere' => $escludi,
            'container' => $container, );

        $testatagriglia = Griglia::testataPerGriglia($paricevuti);

        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 1;
        $testatagriglia['showadd'] = 1;
        $testatagriglia['showedit'] = 1;
        $testatagriglia['showdel'] = 1;

        $testatagriglia['showexcel'] = 1;

        //$testatagriglia["filterToolbar_stringResult"] = false;
        //$testatagriglia["filterToolbar_searchOnEnter"] = true;
        //$testatagriglia["filterToolbar_searchOperators"] = false;
        //$testatagriglia["filterToolbar_clearSearch"] = false;

        $testatagriglia['parametritesta'] = json_encode($paricevuti);
        $this->setParametriGriglia(array('request' => $request));
        $testatagriglia['parametrigriglia'] = json_encode(self::$parametrigriglia);

        $testata = json_encode($testatagriglia);
        $twigparms = array(
            'nomecontroller' => $controller,
            'testata' => $testata,
        );

        return $this->render($nomebundle.':'.$controller.':index.html.twig', $twigparms);
    }

    public function setParametriGriglia($prepar = array())
    {
        self::setup($prepar['request']);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace.$bundle.'Bundle';
        $escludi = array('nota', 'ffprincipale');
        $tabellej['ffprincipale_id'] = array('tabella' => 'ffprincipale', 'campi' => array('descrizione'));

        $campiextra = array(array('lunghezzanota'), array('attivoToString'));
        //$campiextra = array(array("lunghezzanota"));
        //$campiextra = array("lunghezzanota");

        $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
            'nomecampo' => 'intero',
            'operatore' => '>=',
            'valorecampo' => 1, );
        $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
            'nomecampo' => 'data',
            'operatore' => '<=',
            'valorecampo' => date('Y-m-d'),
            'operatorelogico' => 'AND', );

        $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
            'nomecampo' => 'attivo',
            'operatore' => '=',
            'valorecampo' => true,
            'operatorelogico' => 'AND', );

        $paricevuti = array('container' => $this->container,
            'nomebundle' => $nomebundle,
            'tabellej' => $tabellej,
            'nometabella' => $controller,
            'campiextra' => $campiextra,
            'escludere' => $escludi,
            'precondizioniAvanzate' => $precondizioniAvanzate, );

        if ($prepar) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }
}

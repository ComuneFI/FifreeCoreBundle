<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Ffsecondaria controller.
 */
class FfsecondariaController extends FiCoreController
{

    public function indexAction(Request $request)
    {

        $this->setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . 'Bundle';

        $dettaglij = array(
            'descsec' => array(
                array('nomecampo' => 'descsec',
                    'lunghezza' => '400',
                    'descrizione' => 'Descrizione tabella secondaria',
                    'tipo' => 'text',),),
            'ffprincipale_id' => array(
                array('nomecampo' => 'ffprincipale.descrizione',
                    'lunghezza' => '400',
                    'descrizione' => 'Descrizione record principale',
                    'tipo' => 'text',),
            ),
        );
        $escludi = array('nota');

        $campiextra = array(
            array('nomecampo' => 'lunghezzanota', 'descrizione' => 'Lunghezza Nota', 'tipo' => 'integer'),
            array('nomecampo' => 'attivoToString', 'lunghezza' => '80', 'descrizione' => 'Attivo string', 'tipo' => 'text'),
        );

        $paricevuti = array(
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'dettaglij' => $dettaglij,
            'campiextra' => $campiextra,
            'escludere' => $escludi,
            'container' => $container,);

        $testatagriglia = Griglia::testataPerGriglia($paricevuti);

        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 1;
        $testatagriglia['showadd'] = 1;
        $testatagriglia['showedit'] = 1;
        $testatagriglia['showdel'] = 1;

        $testatagriglia['showexcel'] = 1;
        $testatagriglia['showimportexcel'] = 1;

        $testatagriglia["filterToolbar_searchOnEnter"] = true;
        $testatagriglia["filterToolbar_searchOperators"] = true;
        
        $testatagriglia['parametritesta'] = json_encode($paricevuti);
        $this->setParametriGriglia(array('request' => $request));
        $testatagriglia['parametrigriglia'] = json_encode(self::$parametrigriglia);

        $gestionepermessi = $this->get("ficorebundle.gestionepermessi");
        $canRead = ($gestionepermessi->leggere(array('modulo' => $controller)) ? 1 : 0);

        $testata = json_encode($testatagriglia);
        $twigparms = array(
            'nomecontroller' => $controller,
            'testata' => $testata,
            'canread' => $canRead,
        );

        if (!$canRead) {
            throw new AccessDeniedException("Non si hanno i permessi per visualizzare questo contenuto");
        } else {
            return $this->render($nomebundle . ':' . $controller . ':index.html.twig', $twigparms);
        }
    }

    public function setParametriGriglia($prepar = array())
    {
        $this->setup($prepar['request']);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $gestionepermessi = $this->get("ficorebundle.gestionepermessi");
        $canRead = ($gestionepermessi->leggere(array('modulo' => $controller)) ? 1 : 0);
        if (!$canRead) {
            throw new AccessDeniedException("Non si hanno i permessi per visualizzare questo contenuto");
        }

        $nomebundle = $namespace . $bundle . 'Bundle';
        $escludi = array('nota', 'ffprincipale');
        $tabellej = array();
        $precondizioniAvanzate = array();
        $tabellej['ffprincipale_id'] = array('tabella' => 'ffprincipale', 'campi' => array('descrizione'));

        $campiextra = array(array('lunghezzanota'), array('attivoToString'));

        $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
            'nomecampo' => 'intero',
            'operatore' => '>=',
            'valorecampo' => 1,);
        $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
            'nomecampo' => 'data',
            'operatore' => '<=',
            'valorecampo' => date('Y-m-d'),
            'operatorelogico' => 'AND',);

        $paricevuti = array('container' => $this->container,
            'nomebundle' => $nomebundle,
            'tabellej' => $tabellej,
            'nometabella' => $controller,
            'campiextra' => $campiextra,
            'escludere' => $escludi,
            'precondizioniAvanzate' => $precondizioniAvanzate,);

        if (! empty($prepar)) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }
}

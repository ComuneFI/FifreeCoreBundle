<?php

namespace Fi\CoreBundle\Controller;

use IntlDateFormatter;
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

        $ffprincipaleSelect = $this->getComboSelectFfprincipale();

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
                    'tipo' => 'select',
                    'valoricombo' => $ffprincipaleSelect
                ),
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
            'container' => $container,
                /* "ordinecolonne" => array("ffprincipale_id", "descsec", "importo", "intero") */                );

        $griglia = $this->get("ficorebundle.griglia");
        $testatagriglia = $griglia->testataPerGriglia($paricevuti);

        $this->setDefaultGridSettings($testatagriglia);

        $testatagriglia['parametritesta'] = \json_encode($paricevuti);
        $this->setParametriGriglia(array('request' => $request));
        $testatagriglia['parametrigriglia'] = \json_encode(self::$parametrigriglia);

        $gestionepermessi = $this->get("ficorebundle.gestionepermessi");
        $canRead = ($gestionepermessi->leggere(array('modulo' => $controller)) ? 1 : 0);

        $testata = \json_encode($testatagriglia);
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
    private function setDefaultGridSettings(&$testatagriglia)
    {
        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 1;
        $testatagriglia['showadd'] = 1;
        $testatagriglia['showedit'] = 1;
        $testatagriglia['showdel'] = 1;

        $testatagriglia['showexcel'] = 1;

        $testatagriglia["filterToolbar_searchOnEnter"] = true;
        $testatagriglia["filterToolbar_searchOperators"] = true;
        $testatagriglia["sortname"] = "data, descsec";
        $testatagriglia["sortorder"] = "desc";
    }
    private function getComboSelectFfprincipale()
    {
        $em = $this->getDoctrine()->getManager();
        $ffprincipaleSelect = array();
        //Imposta il filtro a TUTTI come default
        $ffprincipaleSelect[] = array("valore" => "", "descrizione" => "Tutti", "default" => true);

        $q = $em->createQueryBuilder();

        $ffprincipales = $q->select('f')
                ->from('FiCoreBundle:Ffprincipale', 'f')
                ->orderBy('f.descrizione')
                ->getQuery()
                ->getResult();

        foreach ($ffprincipales as $ffprincipale) {
            $ffprincipaleSelect[] = array(
                "valore" => $ffprincipale->getDescrizione(),
                "descrizione" => $ffprincipale->getDescrizione(),
                "default" => false);
        }
        return $ffprincipaleSelect;
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

        /* $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
          'nomecampo' => 'intero',
          'operatore' => '>=',
          'valorecampo' => 1,
          'operatorelogico' => 'OR'); */

        //$precondizioni = array('ffprincipale_id' => '1');
        $precondizioni = array();
        $precondizioniAvanzate = array();

        /* $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
          'nomecampo' => 'descsec',
          'operatore' => 'is', //'operatore' => 'not in'
          'valorecampo' => null); */

        $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
            'nomecampo' => 'descsec',
            'operatore' => 'is not', //'operatore' => 'not in'
            'valorecampo' => null);

        /* $listaffsecondaria = array();
          $listaffsecondaria[] = "1° secondaria legato al 1° record PRINCIPALE";
          $listaffsecondaria[] = "2° SECONDARIA legato al 1° record principale";
          $listaffsecondaria[] = "10° secondaria legato al 2° record principale ed è l'ultimo record";
          $listaffsecondaria[] = "6° secondaria legato al 2° record principale";
          $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
          'nomecampo' => 'descsec',
          'operatore' => 'in', //'operatore' => 'not in'
          'valorecampo' => $listaffsecondaria); */

        /* $listaffsecondaria = array();
          $listaffsecondaria[] = "1° secondaria legato al 1° record PRINCIPALE";
          $listaffsecondaria[] = "2° SECONDARIA legato al 1° record principale";
          $listaffsecondaria[] = "10° secondaria legato al 2° record principale ed è l'ultimo record";
          $listaffsecondaria[] = "6° secondaria legato al 2° record principale";
          $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
          'nomecampo' => 'descsec',
          'operatore' => 'not in', //'operatore' => 'not in'
          'valorecampo' => $listaffsecondaria); */

        /* $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
          'nomecampo' => 'intero',
          'operatore' => '=',
          'valorecampo' => 1,
          'operatorelogico' => 'OR',); */

        /* $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
          'nomecampo' => 'intero',
          'operatore' => '<',
          'valorecampo' => 100,
          'operatorelogico' => 'OR',); */

        /* $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
          'nomecampo' => 'data',
          'operatore' => '<=',
          'valorecampo' => date('Y-m-d'),
          'operatorelogico' => 'AND',); */


//        $lista[] = '1° secondaria legato al 1° record principale';
//        $lista[] = '2° secondaria legato al 1° record principale';
//        $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
//            'nomecampo' => 'descsec',
//            'operatore' => 'in',
//            'valorecampo' => $lista,
//            'operatorelogico' => 'AND',);


        $paricevuti = array('container' => $this->container,
            'nomebundle' => $nomebundle,
            'tabellej' => $tabellej,
            'nometabella' => $controller,
            'campiextra' => $campiextra,
            'escludere' => $escludi,
            'precondizioni' => $precondizioni,
            'precondizioniAvanzate' => $precondizioniAvanzate,
                /* "ordinecolonne" => array("ffprincipale_id", "descsec", "importo", "intero") */
        );

        if (!empty($prepar)) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }
}

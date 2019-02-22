<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FiCoreController extends FiController
{
    public function stampatabellaAction(Request $request)
    {
        $this->setup($request);
        $stampaservice = $this->get("ficorebundle.tabelle.stampa.pdf");

        $parametri = $this->prepareOutput($request);

        $stampaservice->stampa($parametri);

        return new Response('OK');
    }
    public function esportaexcelAction(Request $request)
    {
        $this->setup($request);
        $stampaservice = $this->get("ficorebundle.tabelle.stampa.xls");

        $parametri = $this->prepareOutput($request);

        $fileexcel = $stampaservice->esportaexcel($parametri);

        $response = new Response();

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . basename($fileexcel) . '"');

        $response->setContent(file_get_contents($fileexcel));
        if (file_exists($fileexcel)) {
            unlink($fileexcel);
        }

        return $response;
    }
    public function prepareOutput($request)
    {
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . 'Bundle';

        $em = $this->getDoctrine()->getManager();

        $paricevuti = array(
            'nomebundle' => $nomebundle,
            'nometabella' => $request->get('nometabella'),
            'container' => $container,
            'request' => $request,
        );

        $parametripertestatagriglia = $this->getParametersTestataPerGriglia($request, $container, $em, $paricevuti);

        $griglia = $this->get("ficorebundle.griglia");
        $testatagriglia = $griglia->testataPerGriglia($parametripertestatagriglia);

        if ($request->get('titolo')) {
            $testatagriglia['titolo'] = $request->get('titolo');
        }
        $parametridatipergriglia = $this->getParametersDatiPerGriglia($request, $container, $em, $paricevuti);
        $corpogriglia = $griglia->datiPerGriglia($parametridatipergriglia);

        $parametri = array('request' => $request, 'testata' => $testatagriglia, 'griglia' => $corpogriglia);
        return $parametri;
    }
}

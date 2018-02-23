<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FiController extends FiCrudController
{
    protected function setParametriGriglia($prepar = array())
    {
        $this->setup($prepar['request']);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';
        $escludi = array();

        $paricevuti = array('container' => $this->container, 'nomebundle' => $nomebundle, 'nometabella' => $controller, 'escludere' => $escludi);

        if (! empty($prepar)) {
            $paricevuti = array_merge($paricevuti, $prepar);
        }

        self::$parametrigriglia = $paricevuti;
    }

    public function grigliaAction(Request $request)
    {
        $this->setParametriGriglia(array('request' => $request));
        $paricevuti = self::$parametrigriglia;

        return new Response(Griglia::datiPerGriglia($paricevuti));
    }

    protected function elencoModifiche($controller, $id)
    {
        $controllerStorico = 'Storicomodifiche';
        $em = $this->getDoctrine()->getManager();
        $risultato = $em->getRepository('FiCoreBundle:' . $controllerStorico)->findBy(
            array(
                    'nometabella' => $controller,
                    'idtabella' => $id,
                )
        );

        return $risultato;
    }

    protected function getParametersTestataPerGriglia($request, $container, $em, $paricevuti)
    {
        $parametritestarequest = $request->get('parametritesta');
        $parametritesta = array();
        if ($parametritestarequest) {
            $jsonparms = json_decode($parametritestarequest);
            $parametritesta = get_object_vars($jsonparms);
            $parametritesta['container'] = $container;
            $parametritesta['doctrine'] = $em;
            $parametritesta['request'] = $request;
            $parametritesta['output'] = 'stampa';
        }

        return $parametritestarequest ? $parametritesta : $paricevuti;
    }

    protected function getParametersDatiPerGriglia($request, $container, $em, $paricevuti)
    {
        $parametrigriglia = $request->get('parametrigriglia');
        if ($parametrigriglia) {
            $jsonparms = json_decode($parametrigriglia);
            $parametrigriglia = get_object_vars($jsonparms);
            $parametrigriglia['container'] = $container;
            $parametrigriglia['doctrine'] = $em;
            $parametrigriglia['request'] = $request;
            $parametrigriglia['output'] = 'stampa';
        }

        return $parametrigriglia ? $parametrigriglia : $paricevuti;
    }
}

<?php

namespace Fi\CoreBundle\Controller;

use Fi\CoreBundle\Controller\FiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Fi\CoreBundle\Controller\Griglia;
use Fi\CoreBundle\Controller\gestionepermessiController;
use Fi\CoreBundle\Entity\Storicomodifiche;
use Fi\CoreBundle\Form\StoricomodificheType;

/**
 * Storicomodifiche controller.
 *
 */
class StoricomodificheController extends FiCoreController
{

    public function modificheAction(Request $request)
    {
        $nometabella = $request->get("nometabella");
        $nomecampo = $request->get("nomecampo");
        $id = (int) $request->get("id");

        self::setup($request);
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $controller = $this->getController();

        $nomebundle = $namespace . $bundle . 'Bundle';

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($nomebundle . ':' . $controller)->findBy(
            array(
                    'nometabella' => $nometabella,
                    'nomecampo' => $nomecampo,
                    'idtabella' => $id,
                )
        );

        return $this->render('FiCoreBundle:Storicomodifiche:modifiche.html.twig', array("modifiche" => $entity));
    }
}

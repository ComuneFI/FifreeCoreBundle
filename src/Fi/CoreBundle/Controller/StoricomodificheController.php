<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

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

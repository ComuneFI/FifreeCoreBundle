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
class StoricomodificheController extends FiController {

    public function modificheAction(Request $request) {
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

    /**
     * save field modification in history table 
     * 
     * @string $nomebundle
     * @string $controller 
     * @array $changes
     * 
     * 
     */
    public function saveHistory($controller, $changes, $id) {

        $em = $this->getDoctrine()->getManager();

        $adesso = new \DateTime();
        foreach ($changes as $fieldName => $change) {
            $nuovamodifica = new \Fi\CoreBundle\Entity\Storicomodifiche();
            $nuovamodifica->setNometabella($controller);
            $nuovamodifica->setNomecampo($fieldName);
            $nuovamodifica->setIdtabella($id);
            $nuovamodifica->setGiorno($adesso);
            $nuovamodifica->setValoreprecedente($this->getValoreprecedenteImpostare($change));
            $nuovamodifica->setOperatori($this->getUser());
            $em->persist($nuovamodifica);
        }
        $em->flush();
        $em->clear();
    }

    /**
     * check if field is historicized 
     * @string $nomebundle
     * @string $controller tablename
     * @string $indicedato fieldname
     * 
     * return @boolean
     * 
     */
    private function isHistoricized($nomebundle, $controller, $indiceDato) {

        $risposta = false;
        $controllerTabelle = "Tabelle";

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($nomebundle . ':' . $controllerTabelle)->findOneBy(
                array(
                    'nometabella' => $controller,
                    'nomecampo' => $indiceDato
                )
        );

        if ($entity && $entity->isRegistrastorico()) {
            $risposta = true;
        }

        return $risposta;
    }

}

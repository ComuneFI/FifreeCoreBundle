<?php

namespace Fi\CoreBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityRepository;

class StoricomodificheRepository extends EntityRepository
{

    private static $namespace;
    private static $bundle;
    private static $controller;

    public function setup()
    {
        self::$namespace = "Fi";
        self::$bundle = "Core";
        self::$controller = "Storicomodifiche";
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
    public function saveHistory($controller, $changes, $id, $user)
    {
        $this->setup();

        $em = $this->getEntityManager();

        $adesso = new DateTime();
        foreach ($changes as $fieldName => $change) {
            $nuovamodifica = new Storicomodifiche();
            $nuovamodifica->setNometabella($controller);
            $nuovamodifica->setNomecampo($fieldName);
            $nuovamodifica->setIdtabella($id);
            $nuovamodifica->setGiorno($adesso);
            $nuovamodifica->setValoreprecedente($this->getValoreprecedenteImpostare($change));
            $nuovamodifica->setOperatori($user);
            $em->persist($nuovamodifica);
        }
        $em->flush();
        $em->clear();
    }

    private function getValoreprecedenteImpostare($change)
    {
        if (is_object($change)) {
            if ($change instanceof DateTime) {
                $risposta = $change->format('d/m/Y H:i:s');
            } else {
                $risposta = $change->__toString() . " (" . $change->getId() . ")";
            }
        } else {
            $risposta = $change;
        }
        return $risposta;
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
    private function isHistoricized($controller, $indiceDato)
    {

        $risposta = false;
        $controllerTabelle = "Tabelle";

        $em = $this->getEntityManager();
        $entity = $em->getRepository('FiCoreBundle:' . $controllerTabelle)->findOneBy(
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

    /**
     * check if single data is  changed
     *
     * @array $originalData
     * @array $newData
     *
     * return @string
     *
     */
    private function isDataChanged($controller, $datooriginale, $singoloDato, $indiceDato, &$changes)
    {

        if (($datooriginale !== $singoloDato) && $this->isHistoricized($controller, $indiceDato)) {
            $changes[$indiceDato] = $datooriginale;
        }
    }

    /**
     * check if something changes
     *
     * @array $originalData
     * @array $newData
     *
     * return @array
     *
     */
    public function isRecordChanged($nomebundle, $controller, $originalData, $newData)
    {

        $changes = array();
        foreach ($newData as $indiceDato => $singoloDato) {
            $this->isDataChanged($controller, $originalData[$indiceDato], $singoloDato, $indiceDato, $changes);
        }
        return $changes;
    }
}

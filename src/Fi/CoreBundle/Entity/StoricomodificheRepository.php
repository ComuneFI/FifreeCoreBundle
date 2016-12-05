<?php

namespace Fi\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StoricomodificheRepository extends EntityRepository {

    static $namespace;
    static $bundle;
    static $controller;
    static $action;
    static $parametrigriglia;
    protected $annocambiometodo;

    public function setup() {
// $matches = array();
// preg_match('/(.*)\\\(.*)Bundle\\\Entity\\\(.*)Repository/', $this->_entityName, $matches);

        /*
          self::$namespace = isset($matches[1]) ? $matches[1] : "";
          self::$bundle = isset($matches[2]) ? $matches[2] : "";
          self::$controller = isset($matches[3]) ? $matches[3] : "";
         * 
         */
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
    public function saveHistory($controller, $changes, $id, $user) {
        $this->setup();

        $em = $this->getEntityManager();

        $adesso = new \DateTime();
        foreach ($changes as $fieldName => $change) {
            $nuovamodifica = new \Fi\CoreBundle\Entity\Storicomodifiche();
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

    private function getValoreprecedenteImpostare($change) {
        if (is_object($change)) {
            $risposta = $change->__toString() . " (" . $change->getId() . ")";
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
    private function isHistoricized($nomebundle, $controller, $indiceDato) {

        $risposta = false;
        $controllerTabelle = "Tabelle";

        $em = $this->getEntityManager();
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

    /**
     * check if single data is  changed
     * 
     * @array $originalData
     * @array $newData
     * 
     * return @string
     * 
     */
    private function isDataChanged($nomebundle, $controller, $datooriginale, $singoloDato, $indiceDato, &$changes) {

        if (($datooriginale !== $singoloDato) && $this->isHistoricized($nomebundle, $controller, $indiceDato)) {
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
    public function isRecordChanged($nomebundle, $controller, $originalData, $newData) {

        $changes = array();
        foreach ($newData as $indiceDato => $singoloDato) {
            $this->isDataChanged($nomebundle, $controller, $originalData[$indiceDato], $singoloDato, $indiceDato, $changes);
        }
        return $changes;
    }

}

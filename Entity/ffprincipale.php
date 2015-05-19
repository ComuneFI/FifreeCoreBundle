<?php

namespace Fi\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ffprincipale
 */
class ffprincipale {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $descrizione;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ffsecondarias;

    /**
     * Constructor
     */
    public function __construct() {
        $this->ffsecondarias = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set descrizione
     *
     * @param string $descrizione
     * @return ffprincipale
     */
    public function setDescrizione($descrizione) {
        $this->descrizione = $descrizione;

        return $this;
    }

    /**
     * Get descrizione
     *
     * @return string 
     */
    public function getDescrizione() {
        return $this->descrizione;
    }

    /**
     * Add ffsecondarias
     *
     * @param \Fi\CoreBundle\Entity\ffsecondaria $ffsecondarias
     * @return ffprincipale
     */
    public function addFfsecondaria(\Fi\CoreBundle\Entity\ffsecondaria $ffsecondarias) {
        $this->ffsecondarias[] = $ffsecondarias;

        return $this;
    }

    /**
     * Remove ffsecondarias
     *
     * @param \Fi\CoreBundle\Entity\ffsecondaria $ffsecondarias
     */
    public function removeFfsecondaria(\Fi\CoreBundle\Entity\ffsecondaria $ffsecondarias) {
        $this->ffsecondarias->removeElement($ffsecondarias);
    }

    /**
     * Get ffsecondarias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFfsecondarias() {
        return $this->ffsecondarias;
    }

    public function __toString() {
        return $this->getDescrizione();
    }

}
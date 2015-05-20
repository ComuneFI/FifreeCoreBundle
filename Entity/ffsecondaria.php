<?php

namespace Fi\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ffsecondaria
 */
class ffsecondaria {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $descsec;

    /**
     * @var integer
     */
    private $ffprincipale_id;

    /**
     * @var \Fi\CoreBundle\Entity\ffprincipale
     */
    private $ffprincipale;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set descsec
     *
     * @param string $descsec
     * @return ffsecondaria
     */
    public function setDescsec($descsec) {
        $this->descsec = $descsec;

        return $this;
    }

    /**
     * Get descsec
     *
     * @return string 
     */
    public function getDescsec() {
        return $this->descsec;
    }

    /**
     * Set ffprincipale_id
     *
     * @param integer $ffprincipaleId
     * @return ffsecondaria
     */
    public function setFfprincipaleId($ffprincipaleId) {
        $this->ffprincipale_id = $ffprincipaleId;

        return $this;
    }

    /**
     * Get ffprincipale_id
     *
     * @return integer 
     */
    public function getFfprincipaleId() {
        return $this->ffprincipale_id;
    }

    /**
     * Set ffprincipale
     *
     * @param \Fi\CoreBundle\Entity\ffprincipale $ffprincipale
     * @return ffsecondaria
     */
    public function setFfprincipale(\Fi\CoreBundle\Entity\ffprincipale $ffprincipale) {
        $this->ffprincipale = $ffprincipale;

        return $this;
    }

    /**
     * Get ffprincipale
     *
     * @return \Fi\CoreBundle\Entity\ffprincipale 
     */
    public function getFfprincipale() {
        return $this->ffprincipale;
    }

    public function __toString() {
        return $this->getDescsec();
    }

}
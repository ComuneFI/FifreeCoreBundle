<?php

namespace Fi\CoreBundle\Entity;

/**
 * Ffprincipale.
 */
class Ffprincipale
{
    /**
     * @var int
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
     * Constructor.
     */
    public function __construct()
    {
        $this->ffsecondarias = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set descrizione.
     *
     * @param string $descrizione
     *
     * @return ffprincipale
     */
    public function setDescrizione($descrizione)
    {
        $this->descrizione = $descrizione;

        return $this;
    }

    /**
     * Get descrizione.
     *
     * @return string
     */
    public function getDescrizione()
    {
        return $this->descrizione;
    }

    /**
     * Add ffsecondarias.
     *
     * @param \Fi\CoreBundle\Entity\Ffsecondaria $ffsecondarias
     *
     * @return ffprincipale
     */
    public function addFfsecondaria(\Fi\CoreBundle\Entity\Ffsecondaria $ffsecondarias)
    {
        $this->ffsecondarias[] = $ffsecondarias;

        return $this;
    }

    /**
     * Remove ffsecondarias.
     *
     * @param \Fi\CoreBundle\Entity\Ffsecondaria $ffsecondarias
     */
    public function removeFfsecondaria(\Fi\CoreBundle\Entity\Ffsecondaria $Ffsecondarias)
    {
        $this->ffsecondarias->removeElement($ffsecondarias);
    }

    /**
     * Get ffsecondarias.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFfsecondarias()
    {
        return $this->ffsecondarias;
    }

    public function __toString()
    {
        return $this->getDescrizione();
    }
}

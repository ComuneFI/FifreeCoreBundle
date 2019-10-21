<?php

namespace Fi\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @var Collection
     */
    private $ffsecondarias;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->ffsecondarias = new ArrayCollection();
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
     * @param Ffsecondaria $ffsecondarias
     *
     * @return ffprincipale
     */
    public function addFfsecondaria(Ffsecondaria $ffsecondarias)
    {
        $this->ffsecondarias[] = $ffsecondarias;

        return $this;
    }

    /**
     * Remove ffsecondarias.
     *
     * @param Ffsecondaria $ffsecondarias
     */
    public function removeFfsecondaria(Ffsecondaria $Ffsecondarias)
    {
        $this->ffsecondarias->removeElement($Ffsecondarias);
    }

    /**
     * Get ffsecondarias.
     *
     * @return Collection
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

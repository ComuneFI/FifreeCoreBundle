<?php

namespace Fi\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * opzioniTabella
 */
class opzioniTabella
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $tabelle_id;

    /**
     * @var string
     */
    private $descrizione;

    /**
     * @var string
     */
    private $parametro;

    /**
     * @var string
     */
    private $valore;

    /**
     * @var \Fi\CoreBundle\Entity\tabelle
     */
    private $tabelle;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tabelle_id
     *
     * @param integer $tabelleId
     * @return opzioniTabella
     */
    public function setTabelleId($tabelleId)
    {
        $this->tabelle_id = $tabelleId;
    
        return $this;
    }

    /**
     * Get tabelle_id
     *
     * @return integer 
     */
    public function getTabelleId()
    {
        return $this->tabelle_id;
    }

    /**
     * Set descrizione
     *
     * @param string $descrizione
     * @return opzioniTabella
     */
    public function setDescrizione($descrizione)
    {
        $this->descrizione = $descrizione;
    
        return $this;
    }

    /**
     * Get descrizione
     *
     * @return string 
     */
    public function getDescrizione()
    {
        return $this->descrizione;
    }

    /**
     * Set parametro
     *
     * @param string $parametro
     * @return opzioniTabella
     */
    public function setParametro($parametro)
    {
        $this->parametro = $parametro;
    
        return $this;
    }

    /**
     * Get parametro
     *
     * @return string 
     */
    public function getParametro()
    {
        return $this->parametro;
    }

    /**
     * Set valore
     *
     * @param string $valore
     * @return opzioniTabella
     */
    public function setValore($valore)
    {
        $this->valore = $valore;
    
        return $this;
    }

    /**
     * Get valore
     *
     * @return string 
     */
    public function getValore()
    {
        return $this->valore;
    }

    /**
     * Set tabelle
     *
     * @param \Fi\CoreBundle\Entity\tabelle $tabelle
     * @return opzioniTabella
     */
    public function setTabelle(\Fi\CoreBundle\Entity\tabelle $tabelle)
    {
        $this->tabelle = $tabelle;
    
        return $this;
    }

    /**
     * Get tabelle
     *
     * @return \Fi\CoreBundle\Entity\tabelle 
     */
    public function getTabelle()
    {
        return $this->tabelle;
    }
}
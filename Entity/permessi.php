<?php

namespace Fi\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * permessi
 */
class permessi
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $modulo;

    /**
     * @var string
     */
    private $crud;

    /**
     * @var integer
     */
    private $operatori_id;

    /**
     * @var integer
     */
    private $ruoli_id;

    /**
     * @var \Fi\CoreBundle\Entity\operatori
     */
    private $operatori;

    /**
     * @var \Fi\CoreBundle\Entity\ruoli
     */
    private $ruoli;


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
     * Set modulo
     *
     * @param string $modulo
     * @return permessi
     */
    public function setModulo($modulo)
    {
        $this->modulo = $modulo;
    
        return $this;
    }

    /**
     * Get modulo
     *
     * @return string 
     */
    public function getModulo()
    {
        return $this->modulo;
    }

    /**
     * Set crud
     *
     * @param string $crud
     * @return permessi
     */
    public function setCrud($crud)
    {
        $this->crud = $crud;
    
        return $this;
    }

    /**
     * Get crud
     *
     * @return string 
     */
    public function getCrud()
    {
        return $this->crud;
    }

    /**
     * Set operatori_id
     *
     * @param integer $operatoriId
     * @return permessi
     */
    public function setOperatoriId($operatoriId)
    {
        $this->operatori_id = $operatoriId;
    
        return $this;
    }

    /**
     * Get operatori_id
     *
     * @return integer 
     */
    public function getOperatoriId()
    {
        return $this->operatori_id;
    }

    /**
     * Set ruoli_id
     *
     * @param integer $ruoliId
     * @return permessi
     */
    public function setRuoliId($ruoliId)
    {
        $this->ruoli_id = $ruoliId;
    
        return $this;
    }

    /**
     * Get ruoli_id
     *
     * @return integer 
     */
    public function getRuoliId()
    {
        return $this->ruoli_id;
    }

    /**
     * Set operatori
     *
     * @param \Fi\CoreBundle\Entity\operatori $operatori
     * @return permessi
     */
    public function setOperatori(\Fi\CoreBundle\Entity\operatori $operatori = null)
    {
        $this->operatori = $operatori;
    
        return $this;
    }

    /**
     * Get operatori
     *
     * @return \Fi\CoreBundle\Entity\operatori 
     */
    public function getOperatori()
    {
        return $this->operatori;
    }

    /**
     * Set ruoli
     *
     * @param \Fi\CoreBundle\Entity\ruoli $ruoli
     * @return permessi
     */
    public function setRuoli(\Fi\CoreBundle\Entity\ruoli $ruoli = null)
    {
        $this->ruoli = $ruoli;
    
        return $this;
    }

    /**
     * Get ruoli
     *
     * @return \Fi\CoreBundle\Entity\ruoli 
     */
    public function getRuoli()
    {
        return $this->ruoli;
    }
}
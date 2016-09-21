<?php

namespace Fi\CoreBundle\Entity;

/**
 * permessi.
 */
class Permessi
{
    /**
     * @var int
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
     * @var int
     */
    private $operatori_id;

    /**
     * @var int
     */
    private $ruoli_id;

    /**
     * @var \Fi\CoreBundle\Entity\Operatori
     */
    private $operatori;

    /**
     * @var \Fi\CoreBundle\Entity\ruoli
     */
    private $ruoli;

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
     * Set modulo.
     *
     * @param string $modulo
     *
     * @return permessi
     */
    public function setModulo($modulo)
    {
        $this->modulo = $modulo;

        return $this;
    }

    /**
     * Get modulo.
     *
     * @return string
     */
    public function getModulo()
    {
        return $this->modulo;
    }

    /**
     * Set crud.
     *
     * @param string $crud
     *
     * @return permessi
     */
    public function setCrud($crud)
    {
        $this->crud = $crud;

        return $this;
    }

    /**
     * Get crud.
     *
     * @return string
     */
    public function getCrud()
    {
        return $this->crud;
    }

    /**
     * Set operatori_id.
     *
     * @param int $operatoriId
     *
     * @return permessi
     */
    public function setOperatoriId($operatoriId)
    {
        $this->operatori_id = $operatoriId;

        return $this;
    }

    /**
     * Get operatori_id.
     *
     * @return int
     */
    public function getOperatoriId()
    {
        return $this->operatori_id;
    }

    /**
     * Set ruoli_id.
     *
     * @param int $ruoliId
     *
     * @return permessi
     */
    public function setRuoliId($ruoliId)
    {
        $this->ruoli_id = $ruoliId;

        return $this;
    }

    /**
     * Get ruoli_id.
     *
     * @return int
     */
    public function getRuoliId()
    {
        return $this->ruoli_id;
    }

    /**
     * Set operatori.
     *
     * @param \Fi\CoreBundle\Entity\Operatori $operatori
     *
     * @return permessi
     */
    public function setOperatori(\Fi\CoreBundle\Entity\Operatori $operatori = null)
    {
        $this->operatori = $operatori;

        return $this;
    }

    /**
     * Get operatori.
     *
     * @return \Fi\CoreBundle\Entity\Operatori
     */
    public function getOperatori()
    {
        return $this->operatori;
    }

    /**
     * Set ruoli.
     *
     * @param \Fi\CoreBundle\Entity\ruoli $ruoli
     *
     * @return permessi
     */
    public function setRuoli(\Fi\CoreBundle\Entity\ruoli $ruoli = null)
    {
        $this->ruoli = $ruoli;

        return $this;
    }

    /**
     * Get ruoli.
     *
     * @return \Fi\CoreBundle\Entity\ruoli
     */
    public function getRuoli()
    {
        return $this->ruoli;
    }
}

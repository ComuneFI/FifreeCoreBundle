<?php

namespace Fi\CoreBundle\Entity;

/**
 * Storicomodifiche
 */
class Storicomodifiche
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nometabella;

    /**
     * @var string
     */
    private $nomecampo;

    /**
     * @var integer
     */
    private $idtabella;

    /**
     * @var \DateTime
     */
    private $giorno;

    /**
     * @var string
     */
    private $valoreprecedente;


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
     * Set nometabella
     *
     * @param string $nometabella
     *
     * @return Storicomodifiche
     */
    public function setNometabella($nometabella)
    {
        $this->nometabella = $nometabella;

        return $this;
    }

    /**
     * Get nometabella
     *
     * @return string
     */
    public function getNometabella()
    {
        return $this->nometabella;
    }

    /**
     * Set nomecampo
     *
     * @param string $nomecampo
     *
     * @return Storicomodifiche
     */
    public function setNomecampo($nomecampo)
    {
        $this->nomecampo = $nomecampo;

        return $this;
    }

    /**
     * Get nomecampo
     *
     * @return string
     */
    public function getNomecampo()
    {
        return $this->nomecampo;
    }

    /**
     * Set idtabella
     *
     * @param integer $idtabella
     *
     * @return Storicomodifiche
     */
    public function setIdtabella($idtabella)
    {
        $this->idtabella = $idtabella;

        return $this;
    }

    /**
     * Get idtabella
     *
     * @return integer
     */
    public function getIdtabella()
    {
        return $this->idtabella;
    }

    /**
     * Set giorno
     *
     * @param \DateTime $giorno
     *
     * @return Storicomodifiche
     */
    public function setGiorno($giorno)
    {
        $this->giorno = $giorno;

        return $this;
    }

    /**
     * Get giorno
     *
     * @return \DateTime
     */
    public function getGiorno()
    {
        return $this->giorno;
    }

    /**
     * Set valoreprecedente
     *
     * @param string $valoreprecedente
     *
     * @return Storicomodifiche
     */
    public function setValoreprecedente($valoreprecedente)
    {
        $this->valoreprecedente = $valoreprecedente;

        return $this;
    }

    /**
     * Get valoreprecedente
     *
     * @return string
     */
    public function getValoreprecedente()
    {
        return $this->valoreprecedente;
    }
    /**
     * @var integer
     */
    private $operatori_id;

    /**
     * @var \Fi\CoreBundle\Entity\Operatori
     */
    private $operatori;


    /**
     * Set operatoriId
     *
     * @param integer $operatoriId
     *
     * @return Storicomodifiche
     */
    public function setOperatoriId($operatoriId)
    {
        $this->operatori_id = $operatoriId;

        return $this;
    }

    /**
     * Get operatoriId
     *
     * @return integer
     */
    public function getOperatoriId()
    {
        return $this->operatori_id;
    }

    /**
     * Set operatori
     *
     * @param \Fi\CoreBundle\Entity\Operatori $operatori
     *
     * @return Storicomodifiche
     */
    public function setOperatori(\Fi\CoreBundle\Entity\Operatori $operatori = null)
    {
        $this->operatori = $operatori;

        return $this;
    }

    /**
     * Get operatori
     *
     * @return \Fi\CoreBundle\Entity\Operatori
     */
    public function getOperatori()
    {
        return $this->operatori;
    }
}

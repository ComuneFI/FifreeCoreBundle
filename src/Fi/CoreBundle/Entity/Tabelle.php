<?php

namespace Fi\CoreBundle\Entity;

/**
 * Tabelle.
 */
class Tabelle
{
    /**
     * @var int
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
     * @var bool
     */
    private $mostraindex;

    /**
     * @var int
     */
    private $ordineindex;

    /**
     * @var int
     */
    private $larghezzaindex;

    /**
     * @var string
     */
    private $etichettaindex;

    /**
     * @var bool
     */
    private $mostrastampa;

    /**
     * @var int
     */
    private $ordinestampa;

    /**
     * @var int
     */
    private $larghezzastampa;

    /**
     * @var string
     */
    private $etichettastampa;

    /**
     * @var int
     */
    private $operatori_id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $opzioniTabellas;

    /**
     * @var \Fi\CoreBundle\Entity\Operatori
     */
    private $operatori;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->opzioniTabellas = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nometabella.
     *
     * @param string $nometabella
     *
     * @return Tabelle
     */
    public function setNometabella($nometabella)
    {
        $this->nometabella = $nometabella;

        return $this;
    }

    /**
     * Get nometabella.
     *
     * @return string
     */
    public function getNometabella()
    {
        return $this->nometabella;
    }

    /**
     * Set nomecampo.
     *
     * @param string $nomecampo
     *
     * @return Tabelle
     */
    public function setNomecampo($nomecampo)
    {
        $this->nomecampo = $nomecampo;

        return $this;
    }

    /**
     * Get nomecampo.
     *
     * @return string
     */
    public function getNomecampo()
    {
        return $this->nomecampo;
    }

    /**
     * Set mostraindex.
     *
     * @param bool $mostraindex
     *
     * @return Tabelle
     */
    public function setMostraindex($mostraindex)
    {
        $this->mostraindex = $mostraindex;

        return $this;
    }

    /**
     * Get mostraindex.
     *
     * @return bool
     */
    public function hasMostraindex()
    {
        return $this->mostraindex;
    }

    /**
     * Set ordineindex.
     *
     * @param int $ordineindex
     *
     * @return Tabelle
     */
    public function setOrdineindex($ordineindex)
    {
        $this->ordineindex = $ordineindex;

        return $this;
    }

    /**
     * Get ordineindex.
     *
     * @return int
     */
    public function getOrdineindex()
    {
        return $this->ordineindex;
    }

    /**
     * Set larghezzaindex.
     *
     * @param int $larghezzaindex
     *
     * @return Tabelle
     */
    public function setLarghezzaindex($larghezzaindex)
    {
        $this->larghezzaindex = $larghezzaindex;

        return $this;
    }

    /**
     * Get larghezzaindex.
     *
     * @return int
     */
    public function getLarghezzaindex()
    {
        return $this->larghezzaindex;
    }

    /**
     * Set etichettaindex.
     *
     * @param string $etichettaindex
     *
     * @return Tabelle
     */
    public function setEtichettaindex($etichettaindex)
    {
        $this->etichettaindex = $etichettaindex;

        return $this;
    }

    /**
     * Get etichettaindex.
     *
     * @return string
     */
    public function getEtichettaindex()
    {
        return $this->etichettaindex;
    }

    /**
     * Set mostrastampa.
     *
     * @param bool $mostrastampa
     *
     * @return Tabelle
     */
    public function setMostrastampa($mostrastampa)
    {
        $this->mostrastampa = $mostrastampa;

        return $this;
    }

    /**
     * Get mostrastampa.
     *
     * @return bool
     */
    public function hasMostrastampa()
    {
        return $this->mostrastampa;
    }

    /**
     * Set ordinestampa.
     *
     * @param int $ordinestampa
     *
     * @return Tabelle
     */
    public function setOrdinestampa($ordinestampa)
    {
        $this->ordinestampa = $ordinestampa;

        return $this;
    }

    /**
     * Get ordinestampa.
     *
     * @return int
     */
    public function getOrdinestampa()
    {
        return $this->ordinestampa;
    }

    /**
     * Set larghezzastampa.
     *
     * @param int $larghezzastampa
     *
     * @return Tabelle
     */
    public function setLarghezzastampa($larghezzastampa)
    {
        $this->larghezzastampa = $larghezzastampa;

        return $this;
    }

    /**
     * Get larghezzastampa.
     *
     * @return int
     */
    public function getLarghezzastampa()
    {
        return $this->larghezzastampa;
    }

    /**
     * Set etichettastampa.
     *
     * @param string $etichettastampa
     *
     * @return Tabelle
     */
    public function setEtichettastampa($etichettastampa)
    {
        $this->etichettastampa = $etichettastampa;

        return $this;
    }

    /**
     * Get etichettastampa.
     *
     * @return string
     */
    public function getEtichettastampa()
    {
        return $this->etichettastampa;
    }

    /**
     * Set operatoriId.
     *
     * @param int $operatoriId
     *
     * @return Tabelle
     */
    public function setOperatoriId($operatoriId)
    {
        $this->operatori_id = $operatoriId;

        return $this;
    }

    /**
     * Get operatoriId.
     *
     * @return int
     */
    public function getOperatoriId()
    {
        return $this->operatori_id;
    }

    /**
     * Add opzioniTabella.
     *
     * @param \Fi\CoreBundle\Entity\OpzioniTabella $opzioniTabella
     *
     * @return Tabelle
     */
    public function addOpzioniTabella(\Fi\CoreBundle\Entity\OpzioniTabella $opzioniTabella)
    {
        $this->opzioniTabellas[] = $opzioniTabella;

        return $this;
    }

    /**
     * Remove opzioniTabella.
     *
     * @param \Fi\CoreBundle\Entity\OpzioniTabella $opzioniTabella
     */
    public function removeOpzioniTabella(\Fi\CoreBundle\Entity\OpzioniTabella $opzioniTabella)
    {
        $this->opzioniTabellas->removeElement($opzioniTabella);
    }

    /**
     * Get opzioniTabellas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpzioniTabellas()
    {
        return $this->opzioniTabellas;
    }

    /**
     * Set operatori.
     *
     * @param \Fi\CoreBundle\Entity\Operatori $operatori
     *
     * @return Tabelle
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

    public function __toString()
    {
        return $this->nometabella.' ['.$this->nomecampo.']';
    }
}

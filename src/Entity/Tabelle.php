<?php

namespace Fi\CoreBundle\Entity;

/**
 * Tabelle.
 */
class Tabelle {

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
     * @var \Fi\CoreBundle\Entity\Operatori
     */
    private $operatori;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nometabella.
     *
     * @param string $nometabella
     *
     * @return tabelle
     */
    public function setNometabella($nometabella) {
        $this->nometabella = $nometabella;

        return $this;
    }

    /**
     * Get nometabella.
     *
     * @return string
     */
    public function getNometabella() {
        return $this->nometabella;
    }

    /**
     * Set nomecampo.
     *
     * @param string $nomecampo
     *
     * @return tabelle
     */
    public function setNomecampo($nomecampo) {
        $this->nomecampo = $nomecampo;

        return $this;
    }

    /**
     * Get nomecampo.
     *
     * @return string
     */
    public function getNomecampo() {
        return $this->nomecampo;
    }

    /**
     * Set mostraindex.
     *
     * @param bool $mostraindex
     *
     * @return tabelle
     */
    public function setMostraindex($mostraindex) {
        $this->mostraindex = $mostraindex;

        return $this;
    }

    /**
     * Get mostraindex.
     *
     * @return bool
     */
    public function getMostraindex() {
        return $this->isMostraindex();
    }

    /**
     * Get mostraindex.
     *
     * @return bool
     */
    public function isMostraindex() {
        return $this->mostraindex;
    }

    /**
     * Set ordineindex.
     *
     * @param int $ordineindex
     *
     * @return tabelle
     */
    public function setOrdineindex($ordineindex) {
        $this->ordineindex = $ordineindex;

        return $this;
    }

    /**
     * Get ordineindex.
     *
     * @return int
     */
    public function getOrdineindex() {
        return $this->ordineindex;
    }

    /**
     * Set larghezzaindex.
     *
     * @param int $larghezzaindex
     *
     * @return tabelle
     */
    public function setLarghezzaindex($larghezzaindex) {
        $this->larghezzaindex = $larghezzaindex;

        return $this;
    }

    /**
     * Get larghezzaindex.
     *
     * @return int
     */
    public function getLarghezzaindex() {
        return $this->larghezzaindex;
    }

    /**
     * Set etichettaindex.
     *
     * @param string $etichettaindex
     *
     * @return tabelle
     */
    public function setEtichettaindex($etichettaindex) {
        $this->etichettaindex = $etichettaindex;

        return $this;
    }

    /**
     * Get etichettaindex.
     *
     * @return string
     */
    public function getEtichettaindex() {
        return $this->etichettaindex;
    }

    /**
     * Set mostrastampa.
     *
     * @param bool $mostrastampa
     *
     * @return tabelle
     */
    public function setMostrastampa($mostrastampa) {
        $this->mostrastampa = $mostrastampa;

        return $this;
    }

    /**
     * Get mostrastampa.
     *
     * @return bool
     */
    public function getMostrastampa() {
        return $this->isMostrastampa();
    }

    /**
     * Get mostrastampa.
     *
     * @return bool
     */
    public function isMostrastampa() {
        return $this->mostrastampa;
    }

    /**
     * Set ordinestampa.
     *
     * @param int $ordinestampa
     *
     * @return tabelle
     */
    public function setOrdinestampa($ordinestampa) {
        $this->ordinestampa = $ordinestampa;

        return $this;
    }

    /**
     * Get ordinestampa.
     *
     * @return int
     */
    public function getOrdinestampa() {
        return $this->ordinestampa;
    }

    /**
     * Set larghezzastampa.
     *
     * @param int $larghezzastampa
     *
     * @return tabelle
     */
    public function setLarghezzastampa($larghezzastampa) {
        $this->larghezzastampa = $larghezzastampa;

        return $this;
    }

    /**
     * Get larghezzastampa.
     *
     * @return int
     */
    public function getLarghezzastampa() {
        return $this->larghezzastampa;
    }

    /**
     * Set etichettastampa.
     *
     * @param string $etichettastampa
     *
     * @return tabelle
     */
    public function setEtichettastampa($etichettastampa) {
        $this->etichettastampa = $etichettastampa;

        return $this;
    }

    /**
     * Get etichettastampa.
     *
     * @return string
     */
    public function getEtichettastampa() {
        return $this->etichettastampa;
    }

    /**
     * Set operatori_id.
     *
     * @param int $operatoriId
     *
     * @return tabelle
     */
    public function setOperatoriId($operatoriId) {
        $this->operatori_id = $operatoriId;

        return $this;
    }

    /**
     * Get operatori_id.
     *
     * @return int
     */
    public function getOperatoriId() {
        return $this->operatori_id;
    }

    /**
     * Set operatori.
     *
     * @param \Fi\CoreBundle\Entity\Operatori $operatori
     *
     * @return tabelle
     */
    public function setOperatori(\Fi\CoreBundle\Entity\Operatori $operatori = null) {
        $this->operatori = $operatori;

        return $this;
    }

    /**
     * Get operatori.
     *
     * @return \Fi\CoreBundle\Entity\Operatori
     */
    public function getOperatori() {
        return $this->operatori;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $opzioni_tabellas;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->opzioni_tabellas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString() {
        return $this->nometabella . ' [' . $this->nomecampo . ']';
    }

    /**
     * Add opzioni_tabellas.
     *
     * @param \Fi\CoreBundle\Entity\opzioniTabella $opzioniTabellas
     *
     * @return tabelle
     */
    public function addOpzioniTabella(\Fi\CoreBundle\Entity\opzioniTabella $opzioniTabellas) {
        $this->opzioni_tabellas[] = $opzioniTabellas;

        return $this;
    }

    /**
     * Remove opzioni_tabellas.
     *
     * @param \Fi\CoreBundle\Entity\opzioniTabella $opzioniTabellas
     */
    public function removeOpzioniTabella(\Fi\CoreBundle\Entity\opzioniTabella $opzioniTabellas) {
        $this->opzioni_tabellas->removeElement($opzioniTabellas);
    }

    /**
     * Get opzioni_tabellas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpzioniTabellas() {
        return $this->opzioni_tabellas;
    }

}

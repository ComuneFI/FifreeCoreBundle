<?php

namespace Fi\CoreBundle\Entity;

/**
 * Ffsecondaria.
 */
class Ffsecondaria
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $descsec;

    /**
     * @var int
     */
    private $ffprincipale_id;

    /**
     * @var \Fi\CoreBundle\Entity\Ffprincipale
     */
    private $ffprincipale;

    /**
     * @var \DateTime
     */
    private $data;

    /**
     * @var float
     */
    private $importo;

    /**
     * @var string
     */
    private $nota;

    /**
     * @var bool
     */
    private $attivo;

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
     * Set descsec.
     *
     * @param string $descsec
     *
     * @return ffsecondaria
     */
    public function setDescsec($descsec)
    {
        $this->descsec = $descsec;

        return $this;
    }

    /**
     * Get descsec.
     *
     * @return string
     */
    public function getDescsec()
    {
        return $this->descsec;
    }

    /**
     * Set ffprincipale_id.
     *
     * @param int $ffprincipaleId
     *
     * @return ffsecondaria
     */
    public function setFfprincipaleId($ffprincipaleId)
    {
        $this->ffprincipale_id = $ffprincipaleId;

        return $this;
    }

    /**
     * Get ffprincipale_id.
     *
     * @return int
     */
    public function getFfprincipaleId()
    {
        return $this->ffprincipale_id;
    }

    /**
     * Set ffprincipale.
     *
     * @param \Fi\CoreBundle\Entity\Ffprincipale $ffprincipale
     *
     * @return ffsecondaria
     */
    public function setFfprincipale(\Fi\CoreBundle\Entity\Ffprincipale $ffprincipale)
    {
        $this->ffprincipale = $ffprincipale;

        return $this;
    }

    /**
     * Get ffprincipale.
     *
     * @return \Fi\CoreBundle\Entity\Ffprincipale
     */
    public function getFfprincipale()
    {
        return $this->ffprincipale;
    }

    /**
     * Set data.
     *
     * @param \DateTime $data
     *
     * @return Ffsecondaria
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data.
     *
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set importo.
     *
     * @param float $importo
     *
     * @return Ffsecondaria
     */
    public function setImporto($importo)
    {
        $this->importo = $importo;

        return $this;
    }

    /**
     * Get importo.
     *
     * @return float
     */
    public function getImporto()
    {
        return $this->importo;
    }

    /**
     * Set nota.
     *
     * @param string $nota
     *
     * @return Ffsecondaria
     */
    public function setNota($nota)
    {
        $this->nota = $nota;

        return $this;
    }

    /**
     * Get nota.
     *
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Set attivo.
     *
     * @param bool $attivo
     *
     * @return Ffsecondaria
     */
    public function setAttivo($attivo)
    {
        $this->attivo = $attivo;

        return $this;
    }

    /**
     * Get attivo.
     *
     * @return bool
     */
    public function isAttivo()
    {
        return $this->attivo;
    }

    /**
     * @var int
     */
    private $intero;

    /**
     * Set intero.
     *
     * @param int $intero
     *
     * @return Ffsecondaria
     */
    public function setIntero($intero)
    {
        $this->intero = $intero;

        return $this;
    }

    /**
     * Get intero.
     *
     * @return int
     */
    public function getIntero()
    {
        return $this->intero;
    }

    /**
     * Get attivo to string.
     *
     * @return string
     */
    public function getAttivoToString()
    {
        return $this->attivo ? 'SI' : 'NO';
    }

    /**
     * Get intero.
     *
     * @return int
     */
    public function getLunghezzanota()
    {
        return strlen($this->nota);
    }

    public function __toString()
    {
        return $this->getDescsec();
    }


}

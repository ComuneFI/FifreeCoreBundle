<?php

namespace Fi\CoreBundle\Entity;

/**
 * allegati
 */
class allegati
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
     * @var integer
     */
    private $indicetabella;

    /**
     * @var string
     */
    private $allegato;

    /**
     * @var string
     */
    private $allegatofile;

    /**
     * @var \DateTime
     */
    private $datamodifica;


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
     * @return allegati
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
     * Set indicetabella
     *
     * @param integer $indicetabella
     *
     * @return allegati
     */
    public function setIndicetabella($indicetabella)
    {
        $this->indicetabella = $indicetabella;

        return $this;
    }

    /**
     * Get indicetabella
     *
     * @return integer
     */
    public function getIndicetabella()
    {
        return $this->indicetabella;
    }

    /**
     * Set allegato
     *
     * @param string $allegato
     *
     * @return allegati
     */
    public function setAllegato($allegato)
    {
        $this->allegato = $allegato;

        return $this;
    }

    /**
     * Get allegato
     *
     * @return string
     */
    public function getAllegato()
    {
        return $this->allegato;
    }

    /**
     * Set allegatofile
     *
     * @param string $allegatofile
     *
     * @return allegati
     */
    public function setAllegatofile($allegatofile)
    {
        $this->allegatofile = $allegatofile;

        return $this;
    }

    /**
     * Get allegatofile
     *
     * @return string
     */
    public function getAllegatofile()
    {
        return $this->allegatofile;
    }

    /**
     * Set datamodifica
     *
     * @param \DateTime $datamodifica
     *
     * @return allegati
     */
    public function setDatamodifica($datamodifica)
    {
        $this->datamodifica = $datamodifica;

        return $this;
    }

    /**
     * Get datamodifica
     *
     * @return \DateTime
     */
    public function getDatamodifica()
    {
        return $this->datamodifica;
    }
}


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

    public function __toString()
    {
        return $this->getDescsec();
    }
}

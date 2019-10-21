<?php

namespace Fi\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Ruoli.
 */
class Ruoli
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $ruolo;

    /**
     * @var string
     */
    private $paginainiziale;

    /**
     * @var Collection
     */
    private $operatoris;

    /**
     * @var Collection
     */
    private $permessis;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->operatoris = new ArrayCollection();
        $this->permessis = new ArrayCollection();
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
     * Set ruolo.
     *
     * @param string $ruolo
     *
     * @return ruoli
     */
    public function setRuolo($ruolo)
    {
        $this->ruolo = $ruolo;

        return $this;
    }

    /**
     * Get ruolo.
     *
     * @return string
     */
    public function getRuolo()
    {
        return $this->ruolo;
    }

    /**
     * Set paginainiziale.
     *
     * @param string $paginainiziale
     *
     * @return ruoli
     */
    public function setPaginainiziale($paginainiziale)
    {
        $this->paginainiziale = $paginainiziale;

        return $this;
    }

    /**
     * Get paginainiziale.
     *
     * @return string
     */
    public function getPaginainiziale()
    {
        return $this->paginainiziale;
    }

    /**
     * Add operatoris.
     *
     * @param Operatori $operatoris
     *
     * @return ruoli
     */
    public function addOperatori(Operatori $operatoris)
    {
        $this->operatoris[] = $operatoris;

        return $this;
    }

    /**
     * Remove operatoris.
     *
     * @param Operatori $operatoris
     */
    public function removeOperatori(Operatori $operatoris)
    {
        $this->operatoris->removeElement($operatoris);
    }

    /**
     * Get operatoris.
     *
     * @return Collection
     */
    public function getOperatoris()
    {
        return $this->operatoris;
    }

    /**
     * Add permessis.
     *
     * @param Permessi $permessis
     *
     * @return ruoli
     */
    public function addPermessi(Permessi $permessis)
    {
        $this->permessis[] = $permessis;

        return $this;
    }

    /**
     * Remove permessis.
     *
     * @param Permessi $permessis
     */
    public function removePermessi(Permessi $permessis)
    {
        $this->permessis->removeElement($permessis);
    }

    /**
     * Get permessis.
     *
     * @return Collection
     */
    public function getPermessis()
    {
        return $this->permessis;
    }

    public function __toString()
    {
        return $this->getRuolo();
    }

    /**
     * @var bool
     */
    private $is_superadmin;

    /**
     * @var bool
     */
    private $is_admin;

    /**
     * @var bool
     */
    private $is_user;

    /**
     * Set is_superadmin.
     *
     * @param bool $isSuperadmin
     *
     * @return ruoli
     */
    public function setIsSuperadmin($isSuperadmin)
    {
        $this->is_superadmin = $isSuperadmin;

        return $this;
    }

    /**
     * Get is_superadmin.
     *
     * @return bool
     */
    public function isSuperadmin()
    {
        return $this->is_superadmin;
    }

    /**
     * Set is_admin.
     *
     * @param bool $isAdmin
     *
     * @return ruoli
     */
    public function setIsAdmin($isAdmin)
    {
        $this->is_admin = $isAdmin;

        return $this;
    }

    /**
     * Get is_admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Set is_user.
     *
     * @param bool $isUser
     *
     * @return ruoli
     */
    public function setIsUser($isUser)
    {
        $this->is_user = $isUser;

        return $this;
    }

    /**
     * Get is_user.
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->is_user;
    }
}

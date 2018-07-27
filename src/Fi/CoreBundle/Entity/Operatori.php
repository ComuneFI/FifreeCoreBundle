<?php

namespace Fi\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="Fi\CoreBundle\Entity\OperatoriRepository")
 */

/**
 * Operatori.
 */
class Operatori extends BaseUser implements EquatableInterface
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    private $operatore;

    /**
     * @var int
     */
    private $ruoli_id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $permessis;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $storicomodifiches;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tabelles;

    /**
     * @var \Fi\CoreBundle\Entity\ruoli
     */
    private $ruoli;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->permessis = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tabelles = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct();
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
     * Set operatore.
     *
     * @param string $operatore
     *
     * @return operatori
     */
    public function setOperatore($operatore)
    {
        $this->operatore = $operatore;

        return $this;
    }

    /**
     * Get operatore.
     *
     * @return string
     */
    public function getOperatore()
    {
        return $this->operatore;
    }

    /**
     * Set ruoli_id.
     *
     * @param int $ruoliId
     *
     * @return operatori
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
     * Add permessis.
     *
     * @param \Fi\CoreBundle\Entity\permessi $permessis
     *
     * @return operatori
     */
    public function addPermessi(\Fi\CoreBundle\Entity\permessi $permessis)
    {
        $this->permessis[] = $permessis;

        return $this;
    }

    /**
     * Get permessis.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPermessis()
    {
        return $this->permessis;
    }

    /**
     * Remove permessis.
     *
     * @param \Fi\CoreBundle\Entity\permessi $permessis
     */
    public function removePermessi(\Fi\CoreBundle\Entity\permessi $permessis)
    {
        $this->permessis->removeElement($permessis);
    }

    /**
     * Get storicomodifiches.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStoricomodicihes()
    {
        return $this->storicomodifiches;
    }

    /**
     * Add storicomodifiches.
     *
     * @param \Fi\CoreBundle\Entity\storicomodifiche $storicomodifiches
     *
     * @return operatori
     */
    public function addStoricomodifiche(\Fi\CoreBundle\Entity\Storicomodifiche $storicomodifiches)
    {
        $this->storicomodifiches[] = $storicomodifiches;

        return $this;
    }

    /**
     * Remove permessis.
     *
     * @param \Fi\CoreBundle\Entity\storicomodifiche $storicomodifiches
     */
    public function removeStoricomodicihe(\Fi\CoreBundle\Entity\Storicomodifiche $storicomodifiches)
    {
        $this->storicomodifiches->removeElement($storicomodifiches);
    }

    /**
     * Add tabelles.
     *
     * @param \Fi\CoreBundle\Entity\tabelle $tabelles
     *
     * @return operatori
     */
    public function addTabelle(\Fi\CoreBundle\Entity\tabelle $tabelles)
    {
        $this->tabelles[] = $tabelles;

        return $this;
    }

    /**
     * Remove tabelles.
     *
     * @param \Fi\CoreBundle\Entity\tabelle $tabelles
     */
    public function removeTabelle(\Fi\CoreBundle\Entity\tabelle $tabelles)
    {
        $this->tabelles->removeElement($tabelles);
    }

    /**
     * Get tabelles.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTabelles()
    {
        return $this->tabelles;
    }

    /**
     * Set ruoli.
     *
     * @param \Fi\CoreBundle\Entity\ruoli $ruoli
     *
     * @return operatori
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

    public function __toString()
    {
        if ($this->getOperatore()) {
            return $this->getOperatore();
        } else {
            //return '';
            return $this->getUsername();
        }
    }

    /**
     * Add storicomodifich
     *
     * @param \Fi\CoreBundle\Entity\Storicomodifiche $storicomodifich
     *
     * @return Operatori
     */
    public function addStoricomodifich(\Fi\CoreBundle\Entity\Storicomodifiche $storicomodifich)
    {
        $this->storicomodifiches[] = $storicomodifich;

        return $this;
    }

    /**
     * Remove storicomodifich
     *
     * @param \Fi\CoreBundle\Entity\Storicomodifiche $storicomodifich
     */
    public function removeStoricomodifich(\Fi\CoreBundle\Entity\Storicomodifiche $storicomodifich)
    {
        $this->storicomodifiches->removeElement($storicomodifich);
    }

    /**
     * Get storicomodifiches
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStoricomodifiches()
    {
        return $this->storicomodifiches;
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}

<?php

namespace Fi\CoreBundle\Entity;

/**
 * MenuApplicazione.
 */
class MenuApplicazione
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nome;

    /**
     * @var string
     */
    private $percorso;

    /**
     * @var int
     */
    private $padre;

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
     * Set nome.
     *
     * @param string $nome
     *
     * @return menuApplicazione
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome.
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set percorso.
     *
     * @param string $percorso
     *
     * @return menuApplicazione
     */
    public function setPercorso($percorso)
    {
        $this->percorso = $percorso;

        return $this;
    }

    /**
     * Get percorso.
     *
     * @return string
     */
    public function getPercorso()
    {
        return $this->percorso;
    }

    /**
     * Set padre.
     *
     * @param int $padre
     *
     * @return menuApplicazione
     */
    public function setPadre($padre)
    {
        $this->padre = $padre;

        return $this;
    }

    /**
     * Get padre.
     *
     * @return int
     */
    public function getPadre()
    {
        return $this->padre;
    }

    /**
     * @var int
     */
    private $ordine;

    /**
     * Set ordine.
     *
     * @param int $ordine
     *
     * @return menuApplicazione
     */
    public function setOrdine($ordine)
    {
        $this->ordine = $ordine;

        return $this;
    }

    /**
     * Get ordine.
     *
     * @return int
     */
    public function getOrdine()
    {
        return $this->ordine;
    }

    /**
     * @var bool
     */
    private $attivo;

    /**
     * @var string
     */
    private $target;

    /**
     * Set attivo.
     *
     * @param bool $attivo
     *
     * @return menuApplicazione
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
     * Set target.
     *
     * @param string $target
     *
     * @return menuApplicazione
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @var string
     */
    private $tag;

    /**
     * @var bool
     */
    private $notifiche;

    /**
     * @var bool
     */
    private $autorizzazionerichiesta;

    /**
     * Set tag.
     *
     * @param string $tag
     *
     * @return menuApplicazione
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set notifiche.
     *
     * @param bool $notifiche
     *
     * @return menuApplicazione
     */
    public function setNotifiche($notifiche)
    {
        $this->notifiche = $notifiche;

        return $this;
    }

    /**
     * Get notifiche.
     *
     * @return bool
     */
    public function hasNotifiche()
    {
        return $this->notifiche;
    }

    /**
     * Set autorizzazionerichiesta.
     *
     * @param bool $autorizzazionerichiesta
     *
     * @return menuApplicazione
     */
    public function setAutorizzazionerichiesta($autorizzazionerichiesta)
    {
        $this->autorizzazionerichiesta = $autorizzazionerichiesta;

        return $this;
    }

    /**
     * Get autorizzazionerichiesta.
     *
     * @return bool
     */
    public function isAutorizzazionerichiesta()
    {
        return $this->autorizzazionerichiesta;
    }

    /**
     * @var string
     */
    private $percorsonotifiche;

    /**
     * Set percorsonotifiche.
     *
     * @param string $percorsonotifiche
     *
     * @return menuApplicazione
     */
    public function setPercorsonotifiche($percorsonotifiche)
    {
        $this->percorsonotifiche = $percorsonotifiche;

        return $this;
    }

    /**
     * Get percorsonotifiche.
     *
     * @return string
     */
    public function getPercorsonotifiche()
    {
        return $this->percorsonotifiche;
    }

    /**
     * Get attivo
     *
     * @return boolean
     */
    public function getAttivo()
    {
        return $this->attivo;
    }

    /**
     * Get notifiche
     *
     * @return boolean
     */
    public function getNotifiche()
    {
        return $this->notifiche;
    }

    /**
     * Get autorizzazionerichiesta
     *
     * @return boolean
     */
    public function getAutorizzazionerichiesta()
    {
        return $this->autorizzazionerichiesta;
    }
}

<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Fi\CoreBundle\Utils\PermessiSingletonUtility;

/*
 * Se c'è l'accoppiata UTENTE + MODULO allora vale quel permesso
 * Se c'è l'accoppiata RUOLO + MODULO allora vale quel permesso
 * Altrimenti solo MODULO
 * Se non trovo informazioni di sorta, il modulo è chiuso
 */

class GestionePermessi
{

    protected $modulo;
    protected $crud;
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    private function presente($lettera)
    {
        if (stripos($this->crud, $lettera) !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function leggere($parametri = array())
    {
        if (!$this->container->get('security.token_storage')->getToken()) {
            return null;
        }
        if (isset($parametri['modulo'])) {
            $this->modulo = $parametri['modulo'];
        }

        $this->setCrud();

        return $this->presente('R') || ($this->isSuperAdmin()); //SuperAdmin
    }

    public function cancellare($parametri = array())
    {
        if (!$this->container->get('security.token_storage')->getToken()) {
            return null;
        }
        if (isset($parametri['modulo'])) {
            $this->modulo = $parametri['modulo'];
        }
        $this->setCrud();

        return $this->presente('D') || ($this->isSuperAdmin()); //SuperAdmin
    }

    public function creare($parametri = array())
    {
        if (!$this->container->get('security.token_storage')->getToken()) {
            return null;
        }
        if (isset($parametri['modulo'])) {
            $this->modulo = $parametri['modulo'];
        }
        $this->setCrud();

        return $this->presente('C') || ($this->isSuperAdmin()); //SuperAdmin
    }

    public function aggiornare($parametri = array())
    {
        if (!$this->container->get('security.token_storage')->getToken()) {
            return null;
        }

        if (isset($parametri['modulo'])) {
            $this->modulo = $parametri['modulo'];
        }
        $this->setCrud();

        return $this->presente('U') || ($this->isSuperAdmin()); //SuperAdmin
    }

    private function isSuperAdmin()
    {
        $utente = $this->container->get('security.token_storage')->getToken()->getUser()->getId();
        $q = $this->container->get('doctrine')
                ->getRepository('FiCoreBundle:Operatori')
                ->find($utente);

        $isSuperAdmin = false;
        if ($q) {
            if ($q->getRuoli()) {
                $isSuperAdmin = $q->getRuoli()->isSuperadmin();
            }
        }
        return $isSuperAdmin;
    }

    public function sulmenu($parametri = array())
    {
        if (isset($parametri['modulo'])) {
            $this->modulo = $parametri['modulo'];
        }
        $permesso = $this->leggere($parametri) ||
                $this->cancellare($parametri) ||
                $this->creare($parametri) ||
                $this->aggiornare($parametri);

        if ($permesso) {
            return true;
        }

        return false;
    }

    public function setCrud($parametri = array())
    {
        if (isset($parametri['modulo'])) {
            $this->modulo = $parametri['modulo'];
        }

        $utentecorrente = $this->utentecorrente();
        $q = PermessiSingletonUtility::instance(
            $this->container->get('doctrine'),
            $this->modulo,
            $utentecorrente["id"],
            $utentecorrente['ruolo_id']
        )->getPermessi();

        if ($q) {
            $this->crud = $q->getCrud();
            return;
        }

        $this->crud = '';
    }

    public function utentecorrente()
    {
        $utentecorrente = array();

        if (!$this->container->get('security.token_storage')->getToken()) {
            $utentecorrente['nome'] = 'Utente non registrato';
            $utentecorrente['id'] = 0;
            $utentecorrente['ruolo_id'] = 0;

            return $utentecorrente;
        }

        $utente = $this->container->get('security.token_storage')->getToken()->getUser()->getId();
        $q = $this->container->get('doctrine')
                ->getRepository('FiCoreBundle:Operatori')
                ->find($utente);

        $utentecorrente['username'] = $utente;
        $utentecorrente['codice'] = $utente;

        if (!$q) {
            $utentecorrente['nome'] = 'Utente non registrato';
            $utentecorrente['id'] = 0;
            $utentecorrente['ruolo_id'] = 0;

            return $utentecorrente;
        }

        $utentecorrente['nome'] = $q->getOperatore();
        $utentecorrente['id'] = $q->getId();
        $utentecorrente['ruolo_id'] = ($q->getRuoli() ? $q->getRuoli()->getId() : 0);

        return $utentecorrente;
    }

    public function impostaPermessi($parametri = array())
    {
        $risposta = array();

        $risposta['permessiedit'] = $this->aggiornare($parametri);
        $risposta['permessidelete'] = $this->cancellare($parametri);
        $risposta['permessicreate'] = $this->creare($parametri);
        $risposta['permessiread'] = $this->leggere($parametri);

        return $risposta;
    }
}

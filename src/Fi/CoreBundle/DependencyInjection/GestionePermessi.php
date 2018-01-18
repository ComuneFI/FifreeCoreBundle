<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

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

        return $this->presente('R') || ($isSuperAdmin); //SuperAdmin
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

        return $this->presente('D') || ($isSuperAdmin); //SuperAdmin
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

        return $this->presente('C') || ($isSuperAdmin); //SuperAdmin
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

        return $this->presente('U') || ($isSuperAdmin); //SuperAdmin
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

        $q = $this->container->get('doctrine')
                ->getRepository('FiCoreBundle:Permessi')
                ->findOneBy(array('operatori_id' => $utentecorrente['id'], 'modulo' => $this->modulo));

        if ($q) {
            $this->crud = $q->getCrud();

            return;
        }

        $q = $this->container->get('doctrine')
                ->getRepository('FiCoreBundle:Permessi')
                ->findOneBy(array('ruoli_id' => $utentecorrente['ruolo_id'], 'modulo' => $this->modulo, 'operatori_id' => null));

        if ($q) {
            $this->crud = $q->getCrud();

            return;
        }

        $q = $this->container->get('doctrine')
                ->getRepository('FiCoreBundle:Permessi')
                ->findOneBy(array('ruoli_id' => null, 'modulo' => $this->modulo, 'operatori_id' => null));

        if ($q) {
            $this->crud = $q->getCrud();
            return;
        }

        $this->crud = '';
    }

    public function utentecorrente()
    {
        $utentecorrente  = array();
        
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

<?php

namespace Fi\CoreBundle\Controller;

/*
 * Se c'è l'accoppiata UTENTE + MODULO allora vale quel permesso
 * Se c'è l'accoppiata RUOLO + MODULO allora vale quel permesso
 * Altrimenti solo MODULO
 * Se non trovo informazioni di sorta, il modulo è chiuso
 *
 *
 */

class GestionepermessiController extends FiController
{
    protected $modulo;
    protected $crud;

    public function __construct($container = null)
    {
        if ($container) {
            $this->setContainer($container);
        }
    }

    private function presente($lettera)
    {
        if (stripos($this->crud, $lettera) !== false) {
            return true;
        } else {
            return false;
        }
    }

    //var_dump($utente);exit;
    //get("session")->get('utente')
    public function leggereAction($parametri = array())
    {
        if (isset($parametri['modulo'])) {
            $this->modulo = $parametri['modulo'];
        }

        $this->setCrud();

        $utente = $this->getUser()->getId();
        $q = $this->getDoctrine()
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

    public function cancellareAction($parametri = array())
    {
        if (isset($parametri['modulo'])) {
            $this->modulo = $parametri['modulo'];
        }
        $this->setCrud();
        $utente = $this->getUser()->getId();
        $q = $this->getDoctrine()
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

    public function creareAction($parametri = array())
    {
        if (isset($parametri['modulo'])) {
            $this->modulo = $parametri['modulo'];
        }
        $this->setCrud();
        $utente = $this->getUser()->getId();
        $q = $this->getDoctrine()
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

    public function aggiornareAction($parametri = array())
    {
        if (isset($parametri['modulo'])) {
            $this->modulo = $parametri['modulo'];
        }
        $this->setCrud();
        $utente = $this->getUser()->getId();
        $q = $this->getDoctrine()
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

    public function sulmenuAction($parametri = array())
    {
        if (isset($parametri['modulo'])) {
            $this->modulo = $parametri['modulo'];
        }
        $permesso = $this->leggereAction($parametri) ||
                $this->cancellareAction($parametri) ||
                $this->creareAction($parametri) ||
                $this->aggiornareAction($parametri);

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

        $utentecorrente = $this->utentecorrenteAction();

        $q = $this->getDoctrine()
                ->getRepository('FiCoreBundle:Permessi')
                ->findOneBy(array('operatori_id' => $utentecorrente['id'], 'modulo' => $this->modulo));

        if ($q) {
            $this->crud = $q->getCrud();

            return;
        }

        $q = $this->getDoctrine()
                ->getRepository('FiCoreBundle:Permessi')
                ->findOneBy(array('ruoli_id' => $utentecorrente['ruolo_id'], 'modulo' => $this->modulo, 'operatori_id' => null));

        if ($q) {
            //echo $utentecorrente["id"] . " ". $this->modulo . " " . $q->getSqlQuery() . "\n\n";
            $this->crud = $q->getCrud();

            return;
        }

        $q = $this->getDoctrine()
                ->getRepository('FiCoreBundle:Permessi')
                ->findOneBy(array('ruoli_id' => null, 'modulo' => $this->modulo, 'operatori_id' => null));

        if ($q) {
            //echo $q->getSqlQuery() . "\n\n";

            $this->crud = $q->getCrud();

            return;
        }

        $this->crud = '';
    }

    public function chiamato($parametri = array())
    {
        return array('nome' => 'chiamato');
    }

    public function utentecorrenteAction($parametri = array())
    {
        //$utente = sfContext::getInstance()->getUser()->getAttribute('utente');

        if (!$this->getUser()) {
            $utentecorrente['nome'] = 'Utente non registrato';
            $utentecorrente['id'] = 0;
            $utentecorrente['ruolo_id'] = 0;

            return $utentecorrente;
        }

        $utente = $this->getUser()->getId();
        $q = $this->getDoctrine()
                ->getRepository('FiCoreBundle:Operatori')
                ->find($utente);

        $utentecorrente = array();

        //var_dump($q);

        /*
          $doctrine = $this->getDoctrine();
          $q = $doctrine->createQueryBuilder("operatori")->
          ->field("matricola")->equals($utente)
          ->getQuery()
          ->getArrayResult();
         */

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

        $risposta['permessiedit'] = $this->aggiornareAction($parametri);
        $risposta['permessidelete'] = $this->cancellareAction($parametri);
        $risposta['permessicreate'] = $this->creareAction($parametri);
        $risposta['permessiread'] = $this->leggereAction($parametri);

        return $risposta;
    }
}

<?php

namespace Fi\CoreBundle\Controller;

use Fi\CoreBundle\Controller\GestionepermessiController as GestionePermessi;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Menu controller.
 */
class MenuController extends FiController
{
    protected function initGestionePermessi()
    {
        $gestionepermessi = new GestionePermessi();
        $gestionepermessi->setContainer($this->container);

        return $gestionepermessi;
    }

    public function generamenuAction()
    {
        $gestionepermessi = $this->initGestionePermessi();
        $gestionepermessi->utentecorrenteAction();
        $router = $this->get('router')->match('/')['_route'];
        //$homeurl = $this->generateUrl($router, array(), true);

        $risposta[] = array('percorso' => $this->getUrlObject($this->container->getParameter('appname'), $router, ''),
            'nome' => $this->container->getParameter('appname'),
            'target' => '',
        );

        $em = $this->get('doctrine')->getManager();
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('a'));
        $qb->from('FiCoreBundle:MenuApplicazione', 'a');
        $qb->where('a.attivo = :attivo and (a.padre is null or a.padre = 0)');
        $qb->setParameter('attivo', true);
        $qb->orderBy('a.padre', 'ASC');
        $qb->orderBy('a.ordine', 'ASC');
        $menu = $qb->getQuery()->getResult();

        $risposta = array_merge($risposta, $this->getMenu($menu));
        $webdir = $this->get('kernel')->getRootDir().'/../web';
        $pathmanuale = '/uploads/manuale.pdf';

        if (file_exists($webdir.$pathmanuale)) {
            $risposta[] = array('percorso' => $this->getUrlObject('Manuale', $pathmanuale, '_blank'), 'nome' => 'Manuale', 'target' => '_blank');
        }
        /*
         * Questo codice per versioni che usano un symfony inferiore a 2.5
          //Dalla versione 2.6 di symfony cambia la gestione del security
          if (version_compare(Kernel::VERSION, '2.6') >= 0) {
          $securityparm = 'security.token_storage';
          } else {
          $securityparm = 'security.context';
          }
          if ($this->get($securityparm)->getToken()->getProviderKey() === 'secured_area') {
          $username = $this->getUser()->getUsername();
          $urlLogout = $this->generateUrl("fi_autenticazione_signout");
          }

          if ($this->get($securityparm)->getToken()->getProviderKey() === 'main') {
          $username = $this->get($securityparm)->getToken()->getUser()->getUsername();
          $urlLogout = $this->generateUrl("fos_user_security_logout");
          }
         */
        if ($this->get('security.token_storage')->getToken()->getProviderKey() === 'secured_area') {
            $username = $this->getUser()->getUsername();
            $urlLogout = $this->generateUrl('fi_autenticazione_signout');
        }

        if ($this->get('security.token_storage')->getToken()->getProviderKey() === 'main') {
            $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
            $urlLogout = $this->generateUrl('fos_user_security_logout');
        }

        $risposta[] = array('percorso' => $this->getUrlObject($username, '', ''), 'nome' => $username, 'target' => '', // "classe" => "ui-state-disabled",
            'sottolivello' => array(
                array('percorso' => $urlLogout, 'nome' => 'Logout', 'target' => ''),
            ),
        );

        return $this->render('FiCoreBundle:Menu:menu.html.twig', array('risposta' => $risposta));
    }

    protected function getMenu($menu)
    {
        $gestionepermessi = $this->initGestionePermessi();
        $gestionepermessi->utentecorrenteAction();

        $risposta = array();
        $em = $this->get('doctrine')->getManager();

        foreach ($menu as $item) {
            $visualizzare = true;

            if ($item->getAutorizzazionerichiesta()) {
                $visualizzare = $gestionepermessi->sulmenuAction(array('modulo' => $item->getTag()));
            }

            if ($visualizzare) {
                //if (!$item->getPadre()) {
                $qb = $em->createQueryBuilder();
                $qb->select(array('a'));
                $qb->from('FiCoreBundle:MenuApplicazione', 'a');
                $qb->where('a.padre = :padre_id');
                $qb->andWhere('a.attivo = :attivo');
                $qb->orderBy('a.padre', 'ASC');
                $qb->orderBy('a.ordine', 'ASC');
                $qb->setParameter('padre_id', $item->getId());
                $qb->setParameter('attivo', true);
                $submenu = $qb->getQuery()->getResult();

                $sottomenutabelle = $this->getSubMenu($submenu);

                $risposta[] = array(
                    'percorso' => $this->getUrlObject($item->getNome(), $item->getPercorso(), $item->getTarget()),
                    'nome' => $item->getNome(),
                    'sottolivello' => $sottomenutabelle,
                    'target' => $item->getTarget(),
                    'notifiche' => $item->getNotifiche(),
                    'tag' => $item->getTag(),
                    'percorsonotifiche' => $this->getUrlObject($item->getNome(), $item->getPercorsonotifiche(), ''),
                );
                unset($submenu);
                unset($sottomenutabelle);
                //}
            }
        }

        return $risposta;
    }

    protected function getSubMenu($submenu)
    {
        $gestionepermessi = $this->initGestionePermessi();
        $gestionepermessi->utentecorrenteAction();

        $sottomenutabelle = array();
        foreach ($submenu as $subitem) {
            $visualizzare = true;
            if ($subitem->getAutorizzazionerichiesta()) {
                $visualizzare = $gestionepermessi->sulmenuAction(array('modulo' => $subitem->getTag()));
            }

            if ($visualizzare) {
                if ($subitem->getId() == 13) {
                    //var_dump($this->getMenu(array($subitem)));exit;
                }
                $vettoresottomenu = $this->getMenu(array($subitem));
                $sottomenu = $vettoresottomenu[0];

                if (isset($sottomenu['sottolivello']) && count($sottomenu['sottolivello']) > 0) {
                    $sottomenutabelle[] = array_merge($this->getUrlObject($subitem->getNome(), $subitem->getPercorso(), $subitem->getTarget()), array('sottolivello' => $sottomenu['sottolivello']));
                } else {
                    $sottomenutabelle[] = $this->getUrlObject($subitem->getNome(), $subitem->getPercorso(), $subitem->getTarget());
                }
            }
        }

        return $sottomenutabelle;
    }

    protected function getUrlObject($nome, $percorso, $target)
    {
        if ($this->routeExists($percorso)) {
            return array('percorso' => $this->generateUrl($percorso), 'nome' => $nome, 'target' => $target);
        } else {
            //Commentato perchè richiede troppo tempo nel validare se il sito esterno è su o meno
            //quindi si prende per buono, al limite si avrà una pagina non trovata ma il programma
            //non da errore
            //if ($this->urlExists($percorso)) {
            return array('percorso' => $percorso, 'nome' => $nome, 'target' => $target);
            //} else {
            //    return array("percorso" => '', "nome" => "NoRoute:" . $percorso);
            //}
        }
    }

    protected function routeExists($name)
    {
        // I assume that you have a link to the container in your twig extension class
        $router = $this->container->get('router');

        if ((null === $router->getRouteCollection()->get($name)) ? false : true) {
            return true;
        } else {
            return false;
        }
    }

    protected function urlExists($name)
    {
        if ($this->checkUrl($name, false)) {
            return true;
        } else {
            if ($this->checkUrl($name, true)) {
                return true;
            } else {
                return false;
            }
        }
    }

    protected function checkUrl($name, $proxy)
    {
        $ch = curl_init($name);

        curl_setopt($ch, CURLOPT_URL, $name);
        if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, 'proxyhttp.comune.intranet:8080');
        } else {
            curl_setopt($ch, CURLOPT_PROXY, null);
        }
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); //timeout in seconds
        //curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_exec($ch);
        // $retcode > 400 -> not found, $retcode = 200, found.
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($retcode === 200 || $retcode === 401) {
            $exist = true;
        } else {
            $exist = false;
        }
        curl_close($ch);

        return $exist;
    }
}

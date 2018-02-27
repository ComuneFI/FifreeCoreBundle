<?php

namespace Fi\CoreBundle\Controller;

/**
 * Menu controller.
 */
class MenuController extends FiCoreController
{

    protected function initGestionePermessi()
    {
        $gestionepermessi = $this->get("ficorebundle.gestionepermessi");

        return $gestionepermessi;
    }

    public function generamenuAction()
    {
        $router = $this->get('router')->match('/')['_route'];
        $rispostahome = array();
        $rispostahome[] = array('percorso' => $this->getUrlObject($this->container->getParameter('appname'), $router, ''),
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
        $menu = $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getResult();

        $risposta = array_merge($rispostahome, $this->getMenu($menu));
        $webdir = $this->get('kernel')->getRootDir() . '/../web';
        $pathmanuale = '/uploads/manuale.pdf';
        $username = "";
        $urlLogout = "";

        if (file_exists($webdir . $pathmanuale)) {
            $risposta[] = array('percorso' => $this->getUrlObject('Manuale', $pathmanuale, '_blank'), 'nome' => 'Manuale', 'target' => '_blank');
        }

        if ($this->get('security.token_storage')->getToken()->getProviderKey() === 'secured_area') {
            $username = $this->getUser()->getUsername();
            $urlLogout = $this->generateUrl('fi_autenticazione_signout');
        }

        if ($this->get('security.token_storage')->getToken()->getProviderKey() === 'main') {
            $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
            $urlLogout = $this->generateUrl('fos_user_security_logout');
        }

        $risposta[] = array('percorso' => $this->getUrlObject($username, '', ''), 'nome' => $username, 'target' => '',
            'sottolivello' => array(
                array('percorso' => $urlLogout, 'nome' => 'Logout', 'target' => ''),
            ),
        );

        return $this->render('FiCoreBundle:Menu:menu.html.twig', array('risposta' => $risposta));
    }

    protected function getMenu($menu)
    {
        $gestionepermessi = $this->initGestionePermessi();

        $risposta = array();
        $em = $this->get('doctrine')->getManager();

        foreach ($menu as $item) {
            $visualizzare = true;

            if ($item->isAutorizzazionerichiesta()) {
                $visualizzare = $gestionepermessi->sulmenu(array('modulo' => $item->getTag()));
            }

            if ($visualizzare) {
                $qb = $em->createQueryBuilder();
                $qb->select(array('a'));
                $qb->from('FiCoreBundle:MenuApplicazione', 'a');
                $qb->where('a.padre = :padre_id');
                $qb->andWhere('a.attivo = :attivo');
                $qb->orderBy('a.padre', 'ASC');
                $qb->orderBy('a.ordine', 'ASC');
                $qb->setParameter('padre_id', $item->getId());
                $qb->setParameter('attivo', true);
                $submenu = $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getResult();

                $sottomenutabelle = $this->getSubMenu($submenu);

                $risposta[] = array(
                    'percorso' => $this->getUrlObject($item->getNome(), $item->getPercorso(), $item->getTarget()),
                    'nome' => $item->getNome(),
                    'sottolivello' => $sottomenutabelle,
                    'target' => $item->getTarget(),
                    'notifiche' => $item->hasNotifiche(),
                    'tag' => $item->getTag(),
                    'percorsonotifiche' => $this->getUrlObject($item->getNome(), $item->getPercorsonotifiche(), ''),
                );
                unset($submenu);
                unset($sottomenutabelle);
            }
        }

        return $risposta;
    }

    protected function getSubMenu($submenu)
    {
        $gestionepermessi = $this->initGestionePermessi();

        $sottomenutabelle = array();
        foreach ($submenu as $subitem) {
            $visualizzare = true;
            if ($subitem->isAutorizzazionerichiesta()) {
                $visualizzare = $gestionepermessi->sulmenu(array('modulo' => $subitem->getTag()));
            }

            if ($visualizzare) {
                $vettoresottomenu = $this->getMenu(array($subitem));
                $sottomenu = $vettoresottomenu[0];

                if (isset($sottomenu['sottolivello']) && count($sottomenu['sottolivello']) > 0) {
                    $sottolivellomenu = array('sottolivello' => $sottomenu['sottolivello']);
                    $menuobj = $this->getUrlObject($subitem->getNome(), $subitem->getPercorso(), $subitem->getTarget());
                    $sottomenutabelle[] = array_merge($menuobj, $sottolivellomenu);
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
            return array('percorso' => $percorso, 'nome' => $nome, 'target' => $target);
        }
    }

    protected function routeExists($name)
    {
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
        curl_exec($ch);
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

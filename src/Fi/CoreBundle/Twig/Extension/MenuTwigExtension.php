<?php

namespace Fi\CoreBundle\Twig\Extension;

use Fi\CoreBundle\Controller\menu;

class MenuTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'vocimenu' => new \Twig_Function_Method($this, 'vociMenu'),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('menuhtml', array($this, 'menuHtml')),
        );
    }

    public function vociMenu()
    {
        $menu = new menu();
        $risposta = $menu->generamenu();

        return $risposta;
    }

    public function menuHtml($parametri)
    {
        $menu = new menu();
        $html = $menu->generamenu();

        return $html;
    }
}

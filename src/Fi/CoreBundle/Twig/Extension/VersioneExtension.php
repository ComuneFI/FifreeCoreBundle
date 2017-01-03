<?php

namespace Fi\CoreBundle\Twig\Extension;

use \Fi\CoreBundle\Controller\FiVersioneController;

class VersioneExtension extends \Twig_Extension
{

    protected $container;

    public function __construct($container = null)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'versione_tag_git' => new \Twig_SimpleFunction('versioneTagGit', array($this, 'versioneTagGit', 'is_safe' => array('html')))
        );
    }

    public function versioneTagGit()
    {

        FiVersioneController::versione($this->container);
        return FiVersioneController::$versione;
    }

    public function getName()
    {
        return 'fi_versione_extension';
    }
}

<?php

namespace Fi\CoreBundle\Twig\Extension;

use Fi\CoreBundle\Controller\FiVersioneController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class VersioneExtension extends AbstractExtension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('versione_tag_git', array($this, 'versioneTagGit', 'is_safe' => array('html'))),
        );
    }

    public function versioneTagGit()
    {
        FiVersioneController::versione($this->container);

        return FiVersioneController::$versione;
    }
}

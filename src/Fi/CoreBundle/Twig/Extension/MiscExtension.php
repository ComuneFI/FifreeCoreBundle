<?php

namespace Fi\CoreBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class MiscExtension extends AbstractExtension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('serviceExists', array($this, 'serviceExists')),
            new TwigFunction('getParameter', array($this, 'getParameter')),
        );
    }

    public function getParameter($parameter)
    {
        if ($this->container->hasParameter($parameter)) {
            return $this->container->getParameter($parameter);
        } else {
            return '';
        }
    }

    public function serviceExists($service)
    {
        return $this->container->has($service);
    }
}

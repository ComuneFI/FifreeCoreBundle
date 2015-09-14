<?php

namespace Fi\CoreBundle\Twig\Extension;

use CG\Core\ClassUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MiscExtension extends \Twig_Extension {

    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction(
                    'serviceExists', array($this, 'serviceExists')),
            new \Twig_SimpleFunction(
                    'getParameter', array($this, 'getParameter')),
        );
    }

    public function getParameter($parameter) {
        if ($this->container->hasParameter($parameter)) {
            return $this->container->getParameter($parameter);
        } else {
            return "";
        }
    }

    public function serviceExists($service) {
        return $this->container->has($service);
    }

    public function getName() {
        return 'fi_misc_extension';
    }

}

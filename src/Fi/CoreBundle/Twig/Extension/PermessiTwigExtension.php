<?php

namespace Fi\CoreBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class PermessiTwigExtension extends AbstractExtension
{

    protected $requeststack;
    protected $container;

    public function __construct(ContainerInterface $container, RequestStack $request_stack)
    {
        $this->container = $container;
        $this->requeststack = $request_stack;
    }

    /**
     * Get current controller name.
     */
    public function getControllerName()
    {
        $pattern = "#Controller\\\([a-zA-Z]*)Controller#";
        $matches = array();
        preg_match($pattern, $this->requeststack->getCurrentRequest()->get('_controller'), $matches);

        return $matches[1];
    }

    public function getFunctions()
    {
        return array(
                //'permesso' => new \Twig_Function_Method($this, 'controllaPermesso'),
        );
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('permesso', array($this, 'singoloPermesso')),
        );
    }

    public function singoloPermesso($lettera)
    {
        $gestionepermessi = $this->container->get("ficorebundle.gestionepermessi");

        $parametri = array();
        $parametri['modulo'] = $this->getControllerName();
        switch ($lettera) {
            case 'c':
                return $gestionepermessi->creare($parametri);
                break;
            case 'r':
                return $gestionepermessi->leggere($parametri);
                break;
            case 'u':
                return $gestionepermessi->aggiornare($parametri);
                break;
            case 'd':
                return $gestionepermessi->cancellare($parametri);
                break;
            default:
                break;
        }
    }
}

<?php

namespace Fi\CoreBundle\Twig\Extension;

use Fi\CoreBundle\Controller\gestionepermessiController;

class PermessiTwigExtension extends \Twig_Extension {

  protected $requeststack;

  public function __construct($container, $request_stack) {
    $this->container = $container;
    $this->requeststack = $request_stack;
  }

  /**
   * Get current controller name
   */
  public function getControllerName() {
    $pattern = "#Controller\\\([a-zA-Z]*)Controller#";
    $matches = array();
    preg_match($pattern, $this->requeststack->getCurrentRequest()->get('_controller'), $matches);

    return $matches[1];
  }

  public function getFunctions() {
    return array(
            //'permesso' => new \Twig_Function_Method($this, 'controllaPermesso'),
    );
  }

  public function getFilters() {
    return array(
        new \Twig_SimpleFilter('permesso', array($this, 'singoloPermesso')),
    );
  }

  public function singoloPermesso($lettera) {

    $gestionepermessi = new gestionepermessiController();
    $gestionepermessi->setContainer($this->container);

    $parametri = array();
    $parametri["modulo"] = $this->getControllerName();
    switch ($lettera) {
      case "c":
        return $gestionepermessi->creareAction($parametri);
        break;
      case "r":
        return $gestionepermessi->leggereAction($parametri);
        break;
      case "u":
        return $gestionepermessi->aggiornareAction($parametri);
        break;
      case "d":
        return $gestionepermessi->cancellareAction($parametri);
        break;
      default:
        break;
    }
  }

  public function getName() {
    return 'fi_permessi_extension';
  }

}

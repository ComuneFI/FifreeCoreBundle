<?php

namespace Fi\CoreBundle\Twig\Extension;

use Fi\CoreBundle\Controller\fiUtilita;

class UtilitaExtension extends \Twig_Extension {

  protected $loader;
  protected $controller;

  public function __construct(\Twig_LoaderInterface $loader) {
    $this->loader = $loader;
  }

  public function setController($controller) {
    $this->controller = $controller;
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return array(
        new \Twig_SimpleFunction('db2data', array($this, 'getDb2data', 'is_safe' => array('html'))),
    );
  }

  /*
    public function getFilters() {
    return array(
    new \Twig_SimpleFilter('permesso', array($this, 'singoloPermesso')),
    );
    } */

  public function getDb2data($giorno) {
    // highlight_string highlights php code only if '<?php' tag is present.

    return fiUtilita::db2data($giorno, true);
  }

  /**
   * Returns the name of the extension.
   *
   * @return string The extension name
   */
  public function getName() {
    return 'db2data';
  }

}

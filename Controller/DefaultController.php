<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

  public function indexAction() {
    
  }

  public function grigliaAction($name) {
    return $this->render('FiCoreBundle:Default:griglia.html.twig', array('name' => $name));
  }

}

<?php

namespace Fi\CoreBundle\Controller;

class StampatabellaController extends FiCoreController
{
    public function __construct($container = null)
    {
        if ($container) {
            $this->setContainer($container);
        }
    }
    public function stampa($parametri = array())
    {
        $stampaservice = $this->get("ficorebundle.tabelle.stampa.pdf");
        return $stampaservice->stampa($parametri);
    }
    public function esportaexcel($parametri = array())
    {
        $stampaservice = $this->get("ficorebundle.tabelle.stampa.xls");
        return $stampaservice->esportaexcel($parametri);
    }
}

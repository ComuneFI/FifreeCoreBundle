<?php

namespace Fi\CoreBundle\DependencyInjection;

/**
 * Description of Jqgrid.
 *
 * @author manzolo
 */
class GridDati
{

    private $grid;

    public function __construct($testata)
    {
        if (isset($testata["gridtype"])) {
            $gridtype = $testata["gridtype"];
        } else {
            $gridtype = 'Jqgrid';
        }
        $class = '\\Fi\\CoreBundle\\DependencyInjection\\' . $gridtype . "Dati";
        $this->grid = new $class($testata);
    }

    public function getResponse()
    {
        return $this->grid->getResponse();
    }
}

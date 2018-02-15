<?php

namespace Fi\CoreBundle\Utils;

/**
 * Description of Jqgrid.
 *
 * @author manzolo
 */
class GridTestata
{

    private $grid;

    public function __construct($testata)
    {
        if (isset($testata["gridtype"])) {
            $gridtype = $testata["gridtype"];
        } else {
            $gridtype = 'Jqgrid';
        }
        $class = '\\Fi\\CoreBundle\\Utils\\' . $gridtype . "Testata";
        $this->grid = new $class($testata);
    }

    public function getResponse()
    {
        return $this->grid->getResponse();
    }
}

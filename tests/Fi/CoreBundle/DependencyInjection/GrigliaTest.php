<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Fi\CoreBundle\Utils\GrigliaUtils;

class GrigliaTest extends WebTestCase
{

    public function testToCamelCase()
    {
        $parametri = array();
        $parametri['str'] = "manzolo_dev";
        $parametri['primamaiuscola'] = false;
        $strCamela = GrigliaUtils::toCamelCase($parametri);
        $this->assertTrue($strCamela == "manzoloDev");
        $parametri['str'] = "manzolo_dev";
        $parametri['primamaiuscola'] = true;
        $strCamelb = GrigliaUtils::toCamelCase($parametri);
        $this->assertTrue($strCamelb == "ManzoloDev");
        $parametri['str'] = "manzolo_dev_elop";
        $parametri['primamaiuscola'] = true;
        $strCamelc = GrigliaUtils::toCamelCase($parametri);
        $this->assertTrue($strCamelc == "ManzoloDevElop");
        $parametri['str'] = "manzolo_dev_elop_";
        $parametri['primamaiuscola'] = true;
        $strCameld = GrigliaUtils::toCamelCase($parametri);
        $this->assertTrue($strCameld == "ManzoloDevElop_");
    }

}

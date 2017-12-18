<?php

namespace Fi\CoreBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Fi\CoreBundle\DependencyInjection\ArrayFunctions;

class GrigliaTest extends WebTestCase
{

    public function testToCamelCase()
    {
        $parametri = array();
        $parametri['str'] = "manzolo_dev";
        $parametri['primamaiuscola'] = false;
        $strCamel = \Fi\CoreBundle\DependencyInjection\GrigliaUtils::toCamelCase($parametri);
        $this->assertTrue($strCamel == "manzoloDev");
        $parametri['str'] = "manzolo_dev";
        $parametri['primamaiuscola'] = true;
        $strCamel = \Fi\CoreBundle\DependencyInjection\GrigliaUtils::toCamelCase($parametri);
        $this->assertTrue($strCamel == "ManzoloDev");
        $parametri['str'] = "manzolo_dev_elop";
        $parametri['primamaiuscola'] = true;
        $strCamel = \Fi\CoreBundle\DependencyInjection\GrigliaUtils::toCamelCase($parametri);
        $this->assertTrue($strCamel == "ManzoloDevElop");
        $parametri['str'] = "manzolo_dev_elop_";
        $parametri['primamaiuscola'] = true;
        $strCamel = \Fi\CoreBundle\DependencyInjection\GrigliaUtils::toCamelCase($parametri);
        $this->assertTrue($strCamel == "ManzoloDevElop_");
    }

}

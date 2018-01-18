<?php

namespace Fi\CoreBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Fi\CoreBundle\DependencyInjection\PercentualiStringhe;

class PercentualiStringheTest extends WebTestCase
{

    private $parametri = array();

    public function setUp()
    {
        $this->parametri[] = array("stringaa" => "manzi", "stringab" => "manzi", "tolleranza" => 0, "percentuale" => 100);
        $this->parametri[] = array("stringaa" => "manzi", "stringab" => "manzo", "tolleranza" => 0, "percentuale" => 80);
        $this->parametri[] = array("stringaa" => "manzi", "stringab" => "man", "tolleranza" => 0, "percentuale" => 75);
        $this->parametri[] = array("stringaa" => "manzi", "stringab" => "m", "tolleranza" => 0, "percentuale" => 33.333);
        $this->parametri[] = array("stringaa" => "manzi", "stringab" => "cicci", "tolleranza" => 0, "percentuale" => 20);
        $this->parametri[] = array("stringaa" => "manzi", "stringab" => "cicci", "tolleranza" => 100, "percentuale" => 10);
    }

    public function testPercentuali()
    {
        $prcobjarray = new PercentualiStringhe($this->parametri);
        $percs = $prcobjarray->percentualiConfrontoStringheVettore($this->parametri);
        foreach ($this->parametri as $parametro) {
            $testarray = array("stringaa" => $parametro["stringaa"], "stringab" => $parametro["stringab"], "tolleranza" => $parametro["tolleranza"]);
            $prcobj = new PercentualiStringhe($testarray);
            $perc = $prcobj->percentualiConfrontoStringhe($testarray);
            $this->assertEquals($parametro["percentuale"], round($perc, 3));
        }
    }

}

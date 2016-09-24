<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Insieme di funzioni utili
 * FiUtilita.
 *
 * @author Emidio Picariello
 */
class FiUtilitaTest extends KernelTestCase {

    public function setUp() {
        self::bootKernel();
    }

    public function testConfrontoStringe() {
        $fiUtilita = new FiUtilita();
        $parms = array("stringaa" => "manzolo", "stringab" => "manzolo", "tolleranza" => 0);
        $ret = $fiUtilita->percentualiConfrontoStringhe($parms);
        $this->assertEquals($ret, 100);
    }

}

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
        $retperc = $fiUtilita->percentualiConfrontoStringhe($parms);
        $this->assertEquals($retperc, 100);

        $retdata = $fiUtilita->data2db("31/12/2016");
        $this->assertEquals($retdata, "2016-12-31");
        $retdatainv = $fiUtilita->data2db("31/12/2016", true);
        $this->assertEquals($retdatainv, "2016-31-12");
        $retdatasl = $fiUtilita->data2db("31/12/2016", false, true);
        $this->assertEquals($retdatasl, "20161231");
    }

}

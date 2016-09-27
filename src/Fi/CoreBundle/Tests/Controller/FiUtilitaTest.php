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
    }

    public function testDate() {
        $fiUtilita = new FiUtilita();

        $retdata = $fiUtilita->data2db("31/12/2016");
        $this->assertEquals($retdata, "2016-12-31");
        $retdatainv = $fiUtilita->data2db("31/12/2016", true);
        $this->assertEquals($retdatainv, "2016-31-12");
        $retdatasl = $fiUtilita->data2db("31/12/2016", false, true);
        $this->assertEquals($retdatasl, "20161231");
    }

    public function testProSelect() {
        $fiUtilita = new FiUtilita();
        $parametri = array("nomecodice" => "codice", "nomedescrizione" => "descrizione", "elementi" => array(array("codice" => "01", "descrizione" => "Primo")));
        $retoptions = $fiUtilita->proSelect($parametri);
        $this->assertEquals($retoptions, '<option value="01">Primo</option>');
    }

}
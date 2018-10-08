<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Fi\CoreBundle\Controller\FiUtilita;

/**
 * Insieme di funzioni utili
 * FiUtilita.
 *
 * @author Emidio Picariello
 */
class FiUtilitaTest extends KernelTestCase
{

    public function testConfrontoStringe()
    {
        $fiUtilita = new FiUtilita();
        $parms = array('stringaa' => 'manzolo', 'stringab' => 'manzolo', 'tolleranza' => 0);
        $retperc = $fiUtilita->percentualiConfrontoStringhe($parms);
        $this->assertEquals($retperc, 100);
    }

    public function testData2db()
    {
        $fiUtilita = new FiUtilita();

        $retdata = $fiUtilita->data2db('31/12/2016');
        $this->assertEquals($retdata, '2016-12-31');
        $retdatainv = $fiUtilita->data2db('31/12/2016', true);
        $this->assertEquals($retdatainv, '2016-31-12');
        $retdatasl = $fiUtilita->data2db('31/12/2016', false, true);
        $this->assertEquals($retdatasl, '20161231');
    }

    public function testDb2Data()
    {
        $fiUtilita = new FiUtilita();

        $retdata = $fiUtilita->db2data('2016-12-31');
        $this->assertEquals($retdata, '31/12/2016');
        $retdatasl = $fiUtilita->db2data('20161231',true);
        $this->assertEquals($retdata, '31/12/2016');
    }

    public function testProSelect()
    {
        $fiUtilita = new FiUtilita();
        $parametri = array(
            'nomecodice' => 'codice',
            'nomedescrizione' => 'descrizione',
            'elementi' => array(
                array('codice' => '01', 'descrizione' => 'Primo'),
            ),
        );
        $retoptions = $fiUtilita->proSelect($parametri);
        $this->assertEquals($retoptions, '<option value="01">Primo</option>');
    }

    public function testJsonRepsonse()
    {
        $fijson = new \Fi\CoreBundle\DependencyInjection\JsonResponse(-100, "not found");
        $return = $fijson->getArrayResponse();
        $this->assertEquals($return["errcode"], -100);
        $this->assertEquals($return["message"], "not found");
        $returndecode = json_decode($fijson->getEncodedResponse());

        $this->assertEquals($returndecode->errcode, -100);
        $this->assertEquals($returndecode->message, "not found");
    }

    public function testSommaminuti()
    {
        $util = new FiUtilita();
        $retfalse = $util->sommaMinuti(array());
        $this->assertFalse($retfalse);
        $parametri = array("minuti" => array(60,60,240,1));
        $ret = $util->sommaMinuti($parametri);
        $this->assertEquals($ret["ore"], 6);
        $this->assertEquals($ret["minuti"], 1);
    }

}

<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Fi\CoreBundle\DependencyInjection\JsonResponse;

class JsonResponseTest extends WebTestCase
{

    public function testResponse()
    {
        $teststring = "Prova";
        $testerrcode = 0;
        $objresponse = new JsonResponse($testerrcode, $teststring);
        $retarray = $objresponse->getArrayResponse();
        $this->assertEquals($retarray["errcode"], $testerrcode);
        $this->assertEquals($retarray["message"], $teststring);
        $retobj = json_decode($objresponse->getEncodedResponse());
        $this->assertEquals($retobj->errcode, $testerrcode);
        $this->assertEquals($retobj->message, $teststring);
    }

}

<?php

namespace Fi\CoreBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Fi\CoreBundle\DependencyInjection\ArrayFunctions;

class ArrayFunctionsTestCase extends WebTestCase
{

    private $rubrica = array();

    public function setUp()
    {
        parent::setUp();
        $this->rubrica[] = array("matricola" => 99996, "cognome" => "manzi", "nome" => "andrea");
        $this->rubrica[] = array("matricola" => 99994, "cognome" => "piariello", "nome" => "emidio");
        $this->rubrica[] = array("matricola" => 99993, "cognome" => "zarra", "nome" => "zorro");
        $this->rubrica[] = array("matricola" => 99992, "cognome" => "aiazzi", "nome" => "andrea");
        $this->rubrica[] = array("matricola" => 99999, "cognome" => "rossi", "nome" => "mario");
        $this->rubrica[] = array("matricola" => 99998, "cognome" => "bianchi", "nome" => "andrea");
        $this->rubrica[] = array("matricola" => 99997, "cognome" => "verdi", "nome" => "michele");
    }

    public function testOrderBy()
    {

        $client = self::createClient();
        $em = $client->getKernel()->getContainer()->get('doctrine')->getManager();
        $arr = new ArrayFunctions();
        $risultato = $arr->arrayOrderby($this->rubrica, "cognome", SORT_ASC);
        $this->assertEquals(99992, $risultato[0]["matricola"]);
        $this->assertEquals(99993, $risultato[(count($risultato) - 1)]["matricola"]);
    }

    public function testInMultiarray()
    {

        $client = self::createClient();
        $em = $client->getKernel()->getContainer()->get('doctrine')->getManager();
        $arr = new ArrayFunctions();
        $risultato = $arr->inMultiarray("aiazzii", $this->rubrica, "cognome");
        $this->assertFalse($risultato);
        $risultato = $arr->inMultiarray("aiazzi", $this->rubrica, "cognome");
        $this->assertEquals(3, $risultato);
        $risultato = $arr->inMultiarray("andrea", $this->rubrica, "nome");
        $this->assertEquals(0, $risultato);
    }

}

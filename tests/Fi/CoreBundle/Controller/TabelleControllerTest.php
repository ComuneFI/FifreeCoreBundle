<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestUtil;

class TabelleControllerTest extends FifreeTestUtil
{

    /**
     * @test
     */
    public function testIndexTabelle()
    {
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Tabelle');
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Tabelle"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

        $clientnoauth = $this->getClientNonAutorizzato();
        $urlnoauth = '/Tabelle/';
        $clientnoauth->request('GET', $urlnoauth);

        $this->assertEquals(302, $clientnoauth->getResponse()->getStatusCode());
    }

    public function testConfiguraTabelle()
    {
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Tabelle_configura', array("nometabella" => "Ffprincipale"));

        $client->request('POST', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}

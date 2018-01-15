<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class TabelleControllerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     */
    public function testIndexTabelle()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('Tabelle');
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Tabelle"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');
    }

    public function testConfiguraTabelle()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('Tabelle_configura', array("nometabella" => "Ffprincipale"));

        $client->request('POST', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}

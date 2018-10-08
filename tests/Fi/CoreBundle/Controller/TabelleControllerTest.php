<?php

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

    public function testAggiornaTabelle()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('Tabelle_aggiorna', array(
            "etichettaindex" => "",
            "etichettastampa" => "",
            "id" => "12",
            "larghezzaindex" => "",
            "larghezzastampa" => "",
            "mostraindex" => 1,
            "mostrastampa" => 1,
            "oper" => "edit",
            "ordineindex" => 10,
            "ordinestampa" => "",
            "registrastorico" => 0,
        ));

        $client->request('POST', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        $url2 = $client->getContainer()->get('router')->generate('Tabelle_aggiorna', array(
            "etichettaindex" => "",
            "etichettastampa" => "",
            "id" => "12",
            "larghezzaindex" => "",
            "larghezzastampa" => "",
            "mostraindex" => 1,
            "mostrastampa" => 1,
            "oper" => "edit",
            "ordineindex" => "",
            "ordinestampa" => "",
            "registrastorico" => 0,
        ));

        $client->request('POST', $url2);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testGrigliapopupTabelle()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('Tabelle_grigliapopup', array("chiamante" => "Ffprincipale"));

        $client->request('POST', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}

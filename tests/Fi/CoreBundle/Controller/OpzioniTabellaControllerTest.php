<?php

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class OpzioniTabellaControllerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     */
    public function testIndexOpzioniTabella()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('OpzioniTabella');
        $em = $this->getEntityManager();
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="OpzioniTabella"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

    }

}

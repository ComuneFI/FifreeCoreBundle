<?php

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class OperatoriControllerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     */
    public function testIndexOperatori()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('Operatori');
        //$this->assertStringContainsString('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Operatori"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

    }
}

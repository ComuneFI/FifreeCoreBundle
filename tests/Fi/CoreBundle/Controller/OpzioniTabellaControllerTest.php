<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestUtil;

class OpzioniTabellaControllerTest extends FifreeTestUtil
{

    /**
     * @test
     */
    public function testIndexOpzioniTabella()
    {
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('OpzioniTabella');
        $em = $this->getEntityManager();
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="OpzioniTabella"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

        $clientnoauth = $this->getClientNonAutorizzato();
        $urlnoauth = '/OpzioniTabella/';
        $clientnoauth->request('GET', $urlnoauth);

        $this->assertEquals(302, $clientnoauth->getResponse()->getStatusCode());
    }

}

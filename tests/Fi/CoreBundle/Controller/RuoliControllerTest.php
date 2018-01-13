<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestUtil;

class RuoliControllerTest extends FifreeTestUtil
{

    /**
     * @test
     */
    public function testIndexRuoli()
    {
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Ruoli');
        $em = $this->getEntityManager();
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        sleep(1);
        $crawler = new Crawler($client->getResponse()->getContent());
        sleep(1);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Ruoli"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

        $clientnoauth = $this->getClientNonAutorizzato();
        $urlnoauth = '/Ruoli/';
        $clientnoauth->request('GET', $urlnoauth);

        $this->assertEquals(302, $clientnoauth->getResponse()->getStatusCode());
    }

}

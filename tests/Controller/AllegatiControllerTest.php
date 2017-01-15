<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTest;
use Behat\Mink\Mink;
use Behat\Mink\Session;

class AllegatiControllerTest extends FifreeTest
{

    /**
     * @test
     */
    public function testIndexAllegati()
    {
        parent::setUp();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Allegati');
        $em = $this->getEntityManager();
        $this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        sleep(1);
        $crawler = new Crawler($client->getResponse()->getContent());
        sleep(1);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Allegati"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

        $clientnoauth = $this->getClientNonAutorizzato();
        $urlnoauth = '/Allegati/';
        $clientnoauth->request('GET', $urlnoauth);

        $this->assertEquals($this->getClassName(), get_class());
        $this->assertEquals(302, $clientnoauth->getResponse()->getStatusCode());
    }
}

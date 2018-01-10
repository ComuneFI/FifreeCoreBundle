<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestUtil;
use Behat\Mink\Mink;
use Behat\Mink\Session;

class StoricomodificheControllerTest extends FifreeTestUtil
{

    public static function setUpBeforeClass()
    {
        clearcache();
    }
    /**
     * @test
     */
    public function testIndexStoricomodifiche()
    {
        parent::setUp();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Storicomodifiche_container');
        $em = $this->getEntityManager();
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        sleep(1);
        $crawler = new Crawler($client->getResponse()->getContent());
        sleep(1);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Storicomodifiche"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

        $clientnoauth = $this->getClientNonAutorizzato();
        $urlnoauth = '/Storicomodifiche/';
        $clientnoauth->request('GET', $urlnoauth);

        $this->assertEquals($this->getClassName(), get_class());
        $this->assertEquals(302, $clientnoauth->getResponse()->getStatusCode());
    }
}

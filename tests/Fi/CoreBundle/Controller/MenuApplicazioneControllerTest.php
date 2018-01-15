<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class MenuApplicazioneControllerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     */
    public function testIndexMenuApplicazione()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('MenuApplicazione');
        $em = $this->getEntityManager();
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        sleep(1);
        $crawler = new Crawler($client->getResponse()->getContent());
        sleep(1);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="MenuApplicazione"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');
    }

}

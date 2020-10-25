<?php

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class PermessiControllerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     */
    public function testIndexPermessi()
    {
        $client = self::getClient();
        $url = $client->getContainer()->get('router')->generate('Permessi');
        $em = $this->getEntityManager();
        //$this->assertStringContainsString('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Permessi"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

    }

}

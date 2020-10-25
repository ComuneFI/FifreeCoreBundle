<?php

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class RuoliControllerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     */
    public function testIndexRuoli()
    {
        $client = self::getClient();
        $url = $client->getContainer()->get('router')->generate('Ruoli');
        $em = $this->getEntityManager();
        //$this->assertStringContainsString('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        sleep(1);
        $crawler = new Crawler($client->getResponse()->getContent());
        sleep(1);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Ruoli"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

    }

}

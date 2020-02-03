<?php

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestUnauthorizedClient;

class FfsecondariaControllerUnauthorizedTest extends FifreeTestUnauthorizedClient
{

    /**
     * @test
     */
    public function testIndexFfsecondaria()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('Ffsecondaria');
        $em = $this->getEntityManager();
        //$this->assertStringContainsString('DoctrineORMEntityManager', get_class($em));

        $urlnoauth = '/Ffsecondaria/';
        $client->request('GET', $urlnoauth);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}

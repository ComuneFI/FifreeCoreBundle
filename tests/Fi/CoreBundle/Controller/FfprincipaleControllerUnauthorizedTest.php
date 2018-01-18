<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestUnauthorizedClient;

class FfprincipaleControllerUnauthorizedTest extends FifreeTestUnauthorizedClient
{

    /**
     * @test
     */
    public function testIndexFfprincipaleUnauthorized()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('Ffprincipale');
        $em = $this->getEntityManager();
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

}

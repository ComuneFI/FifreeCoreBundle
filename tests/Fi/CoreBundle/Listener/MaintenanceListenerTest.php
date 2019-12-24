<?php

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class MaintenanceListenerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     * @covers Fi\CoreBundle\Listener\MaintenanceListener::<public>
     */
    public function testSitoInManutenzione()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('Ffprincipale');
        $em = $this->getEntityManager();
        //$this->assertStringContainsString('DoctrineORMEntityManager', get_class($em));

        $filelock = $client->getContainer()->getParameter('maintenanceLockFilePath');
        @unlink($filelock);        
        
        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        $undermaintenance = "Sito in manutenzione";
        file_put_contents($filelock, $undermaintenance);
        
        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertEquals($undermaintenance, strip_tags($crawler->html()));
        $this->assertEquals(503, $client->getResponse()->getStatusCode());
        
        unlink($filelock);
        file_put_contents($filelock, "");
        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertEquals("Il sistema è in manutenzione, riprovare tra poco...", strip_tags($crawler->html()));
        $this->assertEquals(503, $client->getResponse()->getStatusCode());
        
        unlink($filelock);
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        
    }

}

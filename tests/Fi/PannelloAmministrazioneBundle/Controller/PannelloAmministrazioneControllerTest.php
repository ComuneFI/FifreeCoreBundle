<?php

namespace Fi\PannelloAmministrazioneBundle\Tests\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestUtil;

class PannelloAmministrazioneControllerTest extends FifreeTestUtil
{

    public static function setUpBeforeClass()
    {
        writestdout("start PannelloAmministrazioneControllerTest");

        cleanFilesystem();
        removecache();
        clearcache();
    }

    /*
     * @test
     */

    public function testIndexAdminpanel()
    {
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_homepage');
        $em = $this->getEntityManager();
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //$body = $crawler->filter('div[id="Ffprincipale"]');
        //$attributes = $body->extract(array('_text', 'class'));
        //$this->assertEquals($attributes[0][1], 'tabella');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        cleanFilesystem();
        removecache();
        clearcache();
    }

}

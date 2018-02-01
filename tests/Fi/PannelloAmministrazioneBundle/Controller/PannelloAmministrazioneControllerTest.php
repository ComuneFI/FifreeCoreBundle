<?php

use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class PannelloAmministrazioneControllerTest extends FifreeTestAuthorizedClient
{
    /*
     * @test
     */

    public function testIndexAdminpanel()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_homepage');
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}

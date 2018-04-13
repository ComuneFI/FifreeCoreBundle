<?php

use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class PannelloAmministrazioneUtilsTest extends FifreeTestAuthorizedClient
{

    public function testClearCache()
    {
        $client = $this->getClient();
        $container = $client->getContainer();
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($container);
        $cachepath = $apppath->getCachePath() . "/prod";


        $pa = new Fi\PannelloAmministrazioneBundle\DependencyInjection\PannelloAmministrazioneUtils($container);
        $pa->clearcache("prod");
        $this->assertTrue(file_exists($cachepath));
    }

}

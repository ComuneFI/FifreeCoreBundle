<?php

use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class PannelloAmministrazioneUtilsTest extends FifreeTestAuthorizedClient
{

    public function testClearCache()
    {
        $client = $this->getClient();

        $pa = new Fi\PannelloAmministrazioneBundle\DependencyInjection\PannelloAmministrazioneUtils($client->getContainer());
        $pa->clearcache("prod");
    }

}

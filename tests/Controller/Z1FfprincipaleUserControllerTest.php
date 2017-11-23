<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeUserTest;
use Behat\Mink\Session;

class Z1FfprincipaleUserControllerTest extends FifreeUserTest
{

    public function testIndexFfprincipaleSenzaPrivilegi()
    {
        parent::setUp();
        $this->setClassName(get_class());

        $client = $this->getClientAutorizzato();
        
        $url = $client->getContainer()->get('router')->generate('Ffprincipale');

        $client->request('GET', $url);
        sleep(1);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

}

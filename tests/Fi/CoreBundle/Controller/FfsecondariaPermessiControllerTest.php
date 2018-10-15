<?php

use Fi\CoreBundle\DependencyInjection\FifreeTestUnauthorizedClient;
use Fi\CoreBundle\Controller\Griglia;
use Symfony\Component\BrowserKit\Cookie;

class FfsecondariaPermessiControllerTest extends FifreeTestUnauthorizedClient
{

    protected static function createUnauthorizedClient($client, $username)
    {
        $container = $client->getContainer();

        $session = $container->get('session');
        /* @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fifree.fos_user.user_manager');
        /* @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('fifree.fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');

        $user = $userManager->findUserBy(array('username' => $username));
        $loginManager->loginUser($firewallName, $user);

        /* save the login token into the session and put it in a cookie */
        $container->get('session')->set('_security_' . $firewallName, serialize($container->get('security.token_storage')->getToken()));
        $container->get('session')->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    /**
     * @test
     */
    public function testFfsecondariaCreateController()
    {
        $clientobj = static::createClient();
        $client = $this->createUnauthorizedClient($clientobj, "ffsecondariac");

        $crawler = $client->request('GET', '/Ffprincipale');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/create');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/1/edit');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/0/delete');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/1/update');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );

        $crawler = $client->request('POST', '/Ffsecondaria/create');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200
        );
        $crawler = $client->request('POST', '/Ffsecondaria/1/edit');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffsecondaria/0/delete');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffsecondaria/1/update');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );


        $crawler = $client->request('GET', '/adminpanel');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Operatori');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Ruoli');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Permessi');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/MenuApplicazione');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/OpzioniTabella');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Tabelle');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
    }

    /**
     * @test
     */
    public function testFfsecondariaReadController()
    {
        $clientobj = static::createClient();
        $client = $this->createUnauthorizedClient($clientobj, "ffsecondariar");

        $crawler = $client->request('GET', '/Ffsecondaria');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200
        );
        $crawler = $client->request('POST', '/Ffprincipale/create');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/1/edit');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/0/delete');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/1/update');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );

        $crawler = $client->request('POST', '/Ffsecondaria/create');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffsecondaria/1/edit');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffsecondaria/0/delete');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffsecondaria/1/update');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );


        $crawler = $client->request('GET', '/adminpanel');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Operatori');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Ruoli');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Permessi');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/MenuApplicazione');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/OpzioniTabella');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Tabelle');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
    }

    /**
     * @test
     */
    public function testFfsecondariaUpdateController()
    {
        $clientobj = static::createClient();
        $client = $this->createUnauthorizedClient($clientobj, "ffsecondariau");

        $crawler = $client->request('GET', '/Ffprincipale');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/create');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Ffprincipale/1/edit');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/0/delete');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/1/update');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );

        $crawler = $client->request('POST', '/Ffsecondaria/create');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffsecondaria/0/delete');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffsecondaria/1/edit');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200
        );


        $form = $crawler->filter('#formdatiFfsecondaria')->form();
        $form->setValues(array(
            "ffsecondaria[attivo]" => 1,
            "ffsecondaria[data][day]" => date("d"),
            "ffsecondaria[data][month]" => date("m"),
            "ffsecondaria[data][year]" => date("Y"),
            "ffsecondaria[descsec]" => "1° secondaria legato al 1° record PRINCIPALE",
            "ffsecondaria[ffprincipale]" => 1,
            "ffsecondaria[importo]" => 12.34,
            "ffsecondaria[intero]" => 10,
            "ffsecondaria[nota]" => "Super Nota ffsecondaria"
        ));

        //$client->setServerParameter("HTTP_X-Requested-With", "XMLHttpRequest");
        $client->submit($form);

        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200
        );


        $crawler = $client->request('GET', '/adminpanel');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Operatori');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Ruoli');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Permessi');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/MenuApplicazione');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/OpzioniTabella');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Tabelle');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
    }

    /**
     * @test
     */
    public function testFfsecondariaDeleteController()
    {
        $clientobj = static::createClient();
        $client = $this->createUnauthorizedClient($clientobj, "ffsecondariad");

        $crawler = $client->request('GET', '/Ffprincipale');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/create');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/1/edit');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/0/delete');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/1/update');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );

        $crawler = $client->request('POST', '/Ffsecondaria/create');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffsecondaria/1/edit');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffsecondaria/0/delete');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200
        );
        $crawler = $client->request('POST', '/Ffsecondaria/1/update');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );


        $crawler = $client->request('GET', '/adminpanel');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Operatori');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Ruoli');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Permessi');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/MenuApplicazione');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/OpzioniTabella');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Tabelle');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
    }

    /**
     * @test
     */
    public function testFfsecondariaFullController()
    {
        $clientobj = static::createClient();
        $client = $this->createUnauthorizedClient($clientobj, "ffsecondaria");

        $crawler = $client->request('GET', '/Ffprincipale');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/create');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/1/edit');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/0/delete');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('POST', '/Ffprincipale/1/update');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );

        $crawler = $client->request('POST', '/Ffsecondaria/create');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200
        );
        $crawler = $client->request('POST', '/Ffsecondaria/0/delete');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200
        );
        $crawler = $client->request('POST', '/Ffsecondaria/1/edit');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200
        );
        $form = $crawler->filter('#formdatiFfsecondaria')->form();
        $form->setValues(array(
            "ffsecondaria[attivo]" => 1,
            "ffsecondaria[data][day]" => date("d"),
            "ffsecondaria[data][month]" => date("m"),
            "ffsecondaria[data][year]" => date("Y"),
            "ffsecondaria[descsec]" => "1° secondaria legato al 1° record PRINCIPALE",
            "ffsecondaria[ffprincipale]" => 1,
            "ffsecondaria[importo]" => 12.34,
            "ffsecondaria[intero]" => 10,
            "ffsecondaria[nota]" => "Super Nota ffsecondaria"
        ));

        //$client->setServerParameter("HTTP_X-Requested-With", "XMLHttpRequest");
        $client->submit($form);
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200
        );


        $crawler = $client->request('GET', '/adminpanel');
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Operatori');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Ruoli');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Permessi');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/MenuApplicazione');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/OpzioniTabella');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
        $crawler = $client->request('GET', '/Tabelle');
        $client->followRedirect();
        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
        );
    }

}

<?php

use Fi\CoreBundle\DependencyInjection\FifreeTestUnauthorizedClient;
use Fi\CoreBundle\Controller\Griglia;
use Symfony\Component\BrowserKit\Cookie;

class PermessiUnhautorizedControllerTest extends FifreeTestUnauthorizedClient
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
    public function testFfprincipaleCreateController()
    {
        $clientobj = static::createClient();
        $client = $this->createUnauthorizedClient($clientobj, "usernoroles");

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
    public function testFfprincipaleReadController()
    {
        $clientobj = static::createClient();
        $client = $this->createUnauthorizedClient($clientobj, "usernoroles");

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
    public function testFfprincipaleUpdateController()
    {
        $clientobj = static::createClient();
        $client = $this->createUnauthorizedClient($clientobj, "usernoroles");

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
    public function testFfprincipaleDeleteController()
    {
        $clientobj = static::createClient();
        $client = $this->createUnauthorizedClient($clientobj, "usernoroles");

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
    public function testFfprincipaleFullController()
    {
        $clientobj = static::createClient();
        $client = $this->createUnauthorizedClient($clientobj, "usernoroles");

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

}

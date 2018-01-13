<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class FifreeTestUtil extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    protected $application;
    protected $container;
    protected $clientNonAutorizzato;
    protected $clientAutorizzato;
    private $testclassname;

    protected function setUp()
    {
        $kernel = new \AppKernel("test", true);
        $kernel->boot();
        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $this->application->setAutoExit(false);

        $this->clientNonAutorizzato = static::createClient();
        $this->clientAutorizzato = $this->createAuthorizedClient(static::createClient());
        $this->container = $this->application->getKernel()->getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
    }

    protected function getContainer()
    {
        return $this->container;
    }

    protected function setClassName($testclassname)
    {
        $this->testclassname = $testclassname;
    }

    protected function getClassName()
    {
        return $this->testclassname;
    }

    protected function getEm()
    {
        return $this->em;
    }

    protected function getEntityManager()
    {
        return $this->em;
    }

    protected function getClientNonAutorizzato()
    {
        return $this->clientNonAutorizzato;
    }

    protected function getClientAutorizzato()
    {
        return $this->clientAutorizzato;
    }

    protected function getControllerNameByClassName()
    {
        $classnamearray = explode('\\', $this->testclassname);
        $classname = $classnamearray[count($classnamearray) - 1];
        $controllerName = preg_replace('/ControllerTest/', '', $classname);

        return $controllerName;
    }

    protected static function createAuthorizedClient($client)
    {
        $container = $client->getContainer();

        $session = $container->get('session');
        /* @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fifree.fos_user.user_manager');
        /* @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('fifree.fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');

        $username4test = $container->getParameter('user4test');
        $user = $userManager->findUserBy(array('username' => $username4test));
        $loginManager->loginUser($firewallName, $user);

        /* save the login token into the session and put it in a cookie */
        $container->get('session')->set('_security_' . $firewallName, serialize($container->get('security.token_storage')->getToken()));
        $container->get('session')->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if (isset($this->em)) {
            $this->em->close();
        }
        parent::tearDown();
    }

    protected function ajaxWait($session, $timeout = 5000)
    {
        $session->wait($timeout, '(0 === jQuery.active)');
        sleep(1);
    }

}

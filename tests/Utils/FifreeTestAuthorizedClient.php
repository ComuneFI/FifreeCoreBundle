<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Behat\Mink\Session;

class FifreeTestAuthorizedClient extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    protected $application;
    protected $container;
    protected $client;
    protected $testclassname;

    protected function setUp()
    {
        $client = static::createClient();
        $this->client = $this->createAuthorizedClient($client);
        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application($this->client->getKernel());
        $this->application->setAutoExit(false);

        $this->container = $this->client->getKernel()->getContainer();
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

    protected function getClient()
    {
        return $this->client;
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
        parent::tearDown();
        if (isset($this->em)) {
            $this->em->close();
        }
    }

}

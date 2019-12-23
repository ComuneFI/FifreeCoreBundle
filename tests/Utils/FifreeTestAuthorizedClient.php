<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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

    protected function setUp() : void
    {
        $client = static::createClient();
        $this->client = $this->createAuthorizedClient($client);
        $this->container = $this->client->getKernel()->getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        /*
        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application($this->client->getKernel());
        $this->application->setAutoExit(false);

         
         */
    }

    protected function getRoute($name, $variables = array(), $absolutepath = false)
    {

        if ($absolutepath) {
            $absolutepath = \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL;
        }

        return $this->client->getContainer()->get('router')->generate($name, $variables, $absolutepath);
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
        $username4test = $container->getParameter('user4test');
        //$user = $userManager->findUserBy(array('username' => $username4test));
        $user = $container->get("doctrine")->getManager()->getRepository("FiCoreBundle:Operatori")
                ->findOneBy(array('username' => $username4test));
        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        //dump($firewallName);exit;
        $container->get('security.token_storage')->setToken($token);
        $session->set('_security_main', serialize($token));
        $session->save();
        /* @var $client \Symfony\Bundle\FrameworkBundle\Client */
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    // @var $userManager \FOS\UserBundle\Doctrine\UserManager
    /*
      $userManager = $container->get('fifree.fos_user.user_manager');
      // @var $loginManager \FOS\UserBundle\Security\LoginManager
      $loginManager = $container->get('fifree.fos_user.security.login_manager');
      $firewallName = $container->getParameter('fos_user.firewall_name');

      $loginManager->loginUser($firewallName, $user);

      // save the login token into the session and put it in a cookie
      $container->get('session')->set('_security_' . $firewallName, serialize($container->get('security.token_storage')->getToken()));
      $container->get('session')->save();
     */

    /**
     * {@inheritdoc}
     */
    protected function tearDown() : void
    {
        parent::tearDown();
        if (isset($this->em)) {
            $this->em->close();
        }
    }

}

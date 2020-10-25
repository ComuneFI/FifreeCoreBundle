<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Behat\Mink\Session;

class FifreeTestUnauthorizedClient extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    protected $application;
    static protected $container;
    static protected $client;
    protected $testclassname;

    protected function setUp() : void
    {
        $client = static::createClient();
        self::$client = $client;

        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application(self::$client->getKernel());
        $this->application->setAutoExit(false);

        self::$container = self::$client->getKernel()->getContainer();
        $this->em = self::$container->get('doctrine')->getManager();
    }

    static protected function getContainer()
    {
        return self::$container;
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

    static protected function getClient()
    {
        return self::$client;
    }

    protected function getControllerNameByClassName()
    {
        $classnamearray = explode('\\', $this->testclassname);
        $classname = $classnamearray[count($classnamearray) - 1];
        $controllerName = preg_replace('/ControllerTest/', '', $classname);

        return $controllerName;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown() : void
    {
        if (isset($this->em)) {
            $this->em->close();
        }
        parent::tearDown();
    }

}

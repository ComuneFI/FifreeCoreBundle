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
    protected $container;
    protected $client;
    protected $testclassname;

    protected function setUp()
    {
        $client = static::createClient();
        $this->client = $client;

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

}

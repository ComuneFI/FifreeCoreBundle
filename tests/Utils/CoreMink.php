<?php

namespace Tests\CoreBundle\Mink;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

abstract class CoreMink extends WebTestCase
{

    /** @var string */
    private $minkBaseUrl;

    /** @var Session */
    protected $minkSession;

    /** @var Client */
    protected $client;

    /** @var Router */
    protected $router;

    /** @var doctrine */
    protected $doctrine;

    /** @var doctrine */
    protected $em;

    /** @var  string */
    protected $seleniumDriverType;

    public function setUp()
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->router = $container->get('router');
        $this->doctrine = $container->get('doctrine');
        $this->em = $container->get('doctrine')->getManager();
        $this->minkBaseUrl = $container->getParameter('mink_url');
        $this->seleniumDriverType = $container->getParameter('selenium_driver_type');
    }

    /**
     * @before
     */
    public function setupMinkSession()
    {
        $driver = new Selenium2Driver($this->seleniumDriverType);
        $this->minkSession = new Session($driver);
        $this->minkSession->start();
    }

    public function getCurrentPage()
    {
        return $this->minkSession->getPage();
    }

    public function getSession()
    {
        return $this->minkSession;
    }

    public function getCurrentPageContent()
    {
        return $this->getCurrentPage()->getContent();
    }

    public function visit($url)
    {
        $this->minkSession->visit($this->minkBaseUrl . $url);
    }

    public function fillField($field, $value)
    {
        $page = $this->getCurrentPage();

        try {
            $page->fillField($field, $value);
        } catch (ElementNotFoundException $ex) {
            $this->screenShot();
            throw($ex);
        }
    }

    public function find($type, $value)
    {
        $page = $this->getCurrentPage();

        try {
            return $page->find($type, $value);
        } catch (ElementNotFoundException $ex) {
            $this->screenShot();
            throw($ex);
        }
    }

    public function findField($type)
    {
        $page = $this->getCurrentPage();

        try {
            return $page->findField($type);
        } catch (ElementNotFoundException $ex) {
            $this->screenShot();
            throw($ex);
        }
    }

    public function pressButton($field)
    {
        $page = $this->getCurrentPage();

        try {
            $page->pressButton($field);
        } catch (ElementNotFoundException $ex) {
            $this->screenShot();
            throw($ex);
        }
    }

    public function login($user, $pass)
    {
        $this->getCurrentPageContent();
        $this->fillField('username', $user);
        $this->fillField('password', $pass);
        $this->pressButton('_submit');
    }

    public function clickLink($field)
    {
        $page = $this->getCurrentPage();
        try {
            $page->clickLink($field);
        } catch (ElementNotFoundException $ex) {
            $this->screenShot();
            throw($ex);
        }
    }

    public function screenShot()
    {
        $driver = $this->minkSession->getDriver();
        if (!($driver instanceof Selenium2Driver)) {
            $this->minkSession->getDriver()->getScreenshot();

            return;
        } else {
            $screenShot = base64_decode($driver->getWebDriverSession()->screenshot());
        }

        $timeStamp = (new \DateTime())->getTimestamp();
        file_put_contents('/tmp/' . $timeStamp . '.png', $screenShot);
        file_put_contents('/tmp/' . $timeStamp . '.html', $this->getCurrentPageContent());
    }

    public function logout()
    {
        $page = $this->getCurrentPage();
        $page->clickLink('logout');
    }

    protected function ajaxWait($session, $timeout = 60000)
    {
        $session->wait($timeout, '(0 === jQuery.active)');
        //sleep(1);
    }

}

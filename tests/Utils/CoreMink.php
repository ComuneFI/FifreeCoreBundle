<?php

namespace Tests\CoreBundle\Mink;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

abstract class CoreMink extends WebTestCase {

    /** @var string */
    private $minkBaseUrl;

    /** @var Session */
    protected $minkSession;

    /** @var Client */
    protected $client;

    /** @var Container */
    protected $container;

    /** @var Router */
    protected $router;

    /** @var doctrine */
    protected $doctrine;

    /** @var doctrine */
    protected $em;

    /** @var  string */
    protected $seleniumDriverType;

    /**
     * @before
     */
    public function setupMinkSession() {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->container = $container;
        $this->router = $container->get('router');
        $this->doctrine = $container->get('doctrine');
        $this->em = $container->get('doctrine')->getManager();
        $this->minkBaseUrl = $container->getParameter('mink_url');
        $this->seleniumDriverType = $container->getParameter('selenium_driver_type');

        //$driver = new \Behat\Mink\Driver\ZombieDriver(new \Behat\Mink\Driver\NodeJS\Server\ZombieServer());
        $driver = new Selenium2Driver($this->seleniumDriverType);
        $this->minkSession = new Session($driver);
        $this->minkSession->start();
    }

    public function getCurrentPage() {
        return $this->minkSession->getPage();
    }

    public function getSession() {
        return $this->minkSession;
    }

    public function getCurrentPageContent() {
        return $this->getCurrentPage()->getContent();
    }

    public function visit($url) {
        $this->minkSession->visit($this->minkBaseUrl . $url);
    }

    public function login($user, $pass) {
        $this->getCurrentPageContent();
        $this->fillField('username', $user);
        $this->fillField('password', $pass);
        $this->pressButton('_submit');
    }

    public function find($selector, $value, $timeout = 4) {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $page = $this->getCurrentPage();
                $element = $page->find($selector, $value);
                if (!$element) {
                    ++$i;
                    sleep(1);
                    continue;
                }
                return $element;
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }
        $this->screenShot();
        throw($e);
    }

    public function findField($selector, $timeout = 4) {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $page = $this->getCurrentPage();
                $element = $page->findField($selector);
                if (!$element) {
                    sleep(1);
                    continue;
                }
                return $element;
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }
        $this->screenShot();
        throw($e);
    }

    public function fillField($selector, $value, $timeout = 4) {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $page = $this->getCurrentPage();
                $page->fillField($selector, $value);
                return;
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }
        $this->screenShot();
        throw($e);
    }

    public function pressButton($selector, $timeout = 4) {

        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $page = $this->getCurrentPage();
                $page->pressButton($selector);
                $this->ajaxWait();
                return;
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }
        $this->screenShot();
        throw($e);
    }

    public function clickLink($selector, $timeout = 4) {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $page = $this->getCurrentPage();
                $page->clickLink($selector);
                $this->ajaxWait();
                return;
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }

        echo $page->getHtml();
        $this->screenShot();
        throw($e);
    }

    public function clickElement($selector, $timeout = 4) {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $page = $this->getCurrentPage();
                $element = $page->find('css', $selector);
                if (!$element) {
                    ++$i;
                    sleep(1);
                    continue;
                }
                $element->click();
                $this->ajaxWait();
                return;
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }

        echo $page->getHtml();
        $this->screenShot();
        throw($e);
    }

    public function dblClickElement($selector, $timeout = 4) {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $page = $this->getCurrentPage();
                $element = $page->find('css', $selector);
                if (!$element) {
                    ++$i;
                    sleep(1);
                    continue;
                }
                $element->doubleClick();
                $this->ajaxWait();
                return;
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }

        echo $page->getHtml();
        $this->screenShot();
        throw($e);
    }

    public function rightClickElement($selector, $timeout = 4) {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $page = $this->getCurrentPage();
                $element = $page->find('css', $selector);
                if (!$element) {
                    ++$i;
                    sleep(1);
                    continue;
                }
                $element->rightClick();
                $this->ajaxWait();
                return;
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }

        echo $page->getHtml();
        $this->screenShot();
        throw($e);
    }

    public function screenShot() {
        $driver = $this->minkSession->getDriver();
        if (!($driver instanceof Selenium2Driver)) {
            if ($driver instanceof \Behat\Mink\Driver\ZombieDriver) {
                return;
            } else {
                $this->minkSession->getDriver()->getScreenshot();
                return;
            }
        } else {
            $screenShot = base64_decode($driver->getWebDriverSession()->screenshot());
        }

        $timeStamp = (new \DateTime())->getTimestamp();
        file_put_contents('/tmp/' . $timeStamp . '.png', $screenShot);
        file_put_contents('/tmp/' . $timeStamp . '.html', $this->getCurrentPageContent());
    }

    public function logout() {
        $this->visit("logout");
    }

    public function tearDown() {
        parent::tearDown();
    }

    protected function ajaxWait($timeout = 60000) {
        $this->getSession()->wait($timeout, '(0 === jQuery.active)');
    }

}

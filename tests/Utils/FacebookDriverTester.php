<?php

namespace Tests\CoreBundle\FacebookDriver;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;

abstract class FacebookDriverTester extends WebTestCase
{

    const TIMEOUT = 2;

    /** @var string */
    private $minkBaseUrl;

    /** @var RemoteWebDriver */
    protected $facebookDriver;

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
    public function setupMinkSession()
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->container = $container;
        $this->router = $container->get('router');
        $this->doctrine = $container->get('doctrine');
        $this->em = $container->get('doctrine')->getManager();

        $this->minkBaseUrl = $container->getParameter('mink_url');
        $this->seleniumDriverType = $container->getParameter('selenium_driver_type');
        //$driver = new \Behat\Mink\Driver\ZombieDriver(new \Behat\Mink\Driver\NodeJS\Server\ZombieServer());
        //$driver = new Selenium2Driver($this->seleniumDriverType);
        $host = 'http://localhost:4444/wd/hub';
        switch ($this->seleniumDriverType) {
            case "firefox":
                $desired_capabilities = DesiredCapabilities::firefox();
                break;
            case "chrome":
                $desired_capabilities = DesiredCapabilities::chrome();
                break;
            default:
                throw new \Exception("Driver non supportato " . $this->seleniumDriverType . ", selezionare firefox o chrome");
                break;
        }
        //$desired_capabilities->setCapability('enablePassThrough', false);
        $driver = RemoteWebDriver::create($host, $desired_capabilities);


        $this->facebookDriver = $driver;
    }

    public function getCurrentPage()
    {
        return $this->facebookDriver;
    }

    public function getSession()
    {
        return $this->facebookDriver;
    }

    public function getCurrentPageContent()
    {
        return $this->facebookDriver->getPageSource();
    }

    public function visit($url)
    {
        $this->facebookDriver->get($this->minkBaseUrl . $url);
    }

    public function login($user, $pass)
    {
        $this->fillField('username', $user);
        $this->fillField('password', $pass);
        $this->pressButton('_submit');
    }

    public function evaluateScript($script)
    {
        return $this->facebookDriver->executeScript($script, array());
    }

    public function executeScript($script)
    {
        return $this->evaluateScript($script);
    }

    public function find($selector, $value, $timeout = self::TIMEOUT)
    {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $page = $this->getCurrentPage();
                $element = $page->find($selector, $value);
                if (!$element || (!$element->isVisible())) {
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

    private function getElementBySelector($selector)
    {
        $element = $this->facebookDriver->findElement(WebDriverBy::id($selector));
        if (!$element) {
            $element = $this->facebookDriver->findElement(WebDriverBy::cssSelector($selector));
            /* @var $element Behat\Mink\Element\NodeElement */
            if (!$element) {
                $element = $this->facebookDriver->findElement(WebDriverBy::name($selector));
                if (!$element) {
                    $element = $this->facebookDriver->findElement(WebDriverBy::tagName($selector));
                    if (!$element) {
                        $element = $this->facebookDriver->findElement(WebDriverBy::className($selector));
                    }
                }
            }
        }
        return $element;
    }

    public function findField($selector, $timeout = self::TIMEOUT)
    {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {

                $element = $this->getElementBySelector($selector);
                /* @var $element Behat\Mink\Element\NodeElement */
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

    public function fillField($selector, $value, $timeout = self::TIMEOUT)
    {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $element = $this->findField($selector);
                if ($element) {
                    $element->clear();
                    $element->sendKeys($value);
                    return;
                }
                ++$i;
                sleep(1);
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }
        $this->screenShot();
        throw($e);
    }

    public function checkboxSelect($selector, $value, $timeout = self::TIMEOUT)
    {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $select = $this->findField($selector);
                if ($select) {
                    if ($select->isSelected() != $value) {
                        dump($select->isSelected());
                        dump($value);
                        $select->click();
                        //$select->click();
                    }
                    return;
                }
                ++$i;
                sleep(1);
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }
        $this->screenShot();
        throw($e);
    }

    public function selectFieldOption($selector, $value, $timeout = self::TIMEOUT)
    {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $select = $this->findField($selector);
                if ($select) {
                    $select->findElement(WebDriverBy::cssSelector("option[value='" . $value . "']"))
                            ->click();
                    return;
                }
                ++$i;
                sleep(1);
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }
        $this->screenShot();
        throw($e);
    }

    public function pressButton($selector, $timeout = self::TIMEOUT)
    {

        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $button = $this->getElementBySelector($selector);
                $button->click();
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

    public function clickLink($selector, $timeout = self::TIMEOUT)
    {
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

    public function clickElement($selector, $timeout = self::TIMEOUT)
    {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $element = $this->getElementBySelector($selector);
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

        $this->screenShot();
        throw($e);
    }

    public function dblClickElement($selector, $timeout = self::TIMEOUT)
    {
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

    public function rightClickElement($selector, $timeout = self::TIMEOUT)
    {
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

    public function screenShot()
    {
        /* $driver = $this->minkSession->getDriver();
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
         */
    }

    public function logout()
    {
        $this->visit("logout");
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * waitForAjax : wait for all ajax request to close
     * @param  integer $timeout  timeout in seconds
     * @param  integer $interval interval in miliseconds
     * @return void            
     */
    public function ajaxWait($timeout = 5, $interval = 200)
    {
        $this->facebookDriver->wait($timeout, $interval)->until(function() {
            // jQuery: "jQuery.active" or $.active
            // Prototype: "Ajax.activeRequestCount"
            // Dojo: "dojo.io.XMLHTTPTransport.inFlight.length"
            $condition = 'return ($.active == 0);';
            return $this->facebookDriver->executeScript($condition);
        });
    }

}

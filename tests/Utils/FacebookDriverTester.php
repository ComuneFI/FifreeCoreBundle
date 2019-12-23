<?php

namespace Tests\CoreBundle\FacebookDriver;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverExpectedCondition;

abstract class FacebookDriverTester extends WebTestCase
{

    const TIMEOUT = 4;

    /** @var string */
    private $facebookDriverUrl;

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

        $this->facebookDriverUrl = $container->getParameter('facebookdriver_url');
        $facebookDriverHost = $container->getParameter('facebookdriver_host');
        $browserHeadless = $container->getParameter('browser_headless');

        $this->seleniumDriverType = $container->getParameter('selenium_driver_type');
        //$driver = new \Behat\Mink\Driver\ZombieDriver(new \Behat\Mink\Driver\NodeJS\Server\ZombieServer());
        //$driver = new Selenium2Driver($this->seleniumDriverType);

        switch ($this->seleniumDriverType) {
            case "firefox":
                $desired_capabilities = DesiredCapabilities::firefox();
                if ($browserHeadless) {
                    $desired_capabilities->setCapability(
                            'moz:firefoxOptions', ['args' => ['-headless']]
                    );
                }
                break;
            case "chrome":
                $desired_capabilities = DesiredCapabilities::chrome();
                $chromeOptions = new ChromeOptions();
                $chromeopt = array();
                $chromeopt[] = 'window-size=1024,768';
                $chromeopt[] = '--no-sandbox';

                if ($browserHeadless) {
                    $chromeopt[] = 'disable-gpu';
                    $chromeopt[] = 'headless';
                }
                $chromeOptions->addArguments($chromeopt);
                $desired_capabilities->setCapability(ChromeOptions::CAPABILITY, $chromeOptions);
                break;
            default:
                throw new \Exception("Driver non supportato " . $this->seleniumDriverType . ", selezionare firefox o chrome");
                break;
        }
        //$desired_capabilities->setCapability('enablePassThrough', false);
        $driver = RemoteWebDriver::create($facebookDriverHost, $desired_capabilities);


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
        $this->facebookDriver->get($this->facebookDriverUrl . $url);
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
        return $this->getElementByWebDriverBy($selector);
    }
    private function getElementByWebDriver($webdriverby)
    {
        $elements = $this->facebookDriver->findElements($webdriverby);
        if (count($elements) === 0) {
            return null;
        } else {
            return $elements[0];
        }
    }
    private function getElementByWebDriverBy($selector)
    {
        $element = $this->getElementByWebDriver(WebDriverBy::id($selector));
        if ($element) {
            return $element;
        }
        $element = $this->getElementByWebDriver(WebDriverBy::cssSelector($selector));
        if ($element) {
            return $element;
        }
        $element = $this->getElementByWebDriver(WebDriverBy::name($selector));
        if ($element) {
            return $element;
        }
        $element = $this->getElementByWebDriver(WebDriverBy::className($selector));
        if ($element) {
            return $element;
        }
        $element = $this->getElementByWebDriver(WebDriverBy::linkText($selector));
        if ($element) {
            return $element;
        }
        $element = $this->getElementByWebDriver(WebDriverBy::xpath($selector));
        if ($element) {
            return $element;
        }
        $element = $this->getElementByWebDriver(WebDriverBy::tagName($selector));
        if ($element) {
            return $element;
        }

        return null;
    }
    private function getWebDriverBy($selector)
    {
        $element = $this->getElementByWebDriver(WebDriverBy::id($selector));
        if ($element) {
            return WebDriverBy::id($selector);
        }
        $element = $this->getElementByWebDriver(WebDriverBy::cssSelector($selector));
        if ($element) {
            return WebDriverBy::cssSelector($selector);
        }
        $element = $this->getElementByWebDriver(WebDriverBy::name($selector));
        if ($element) {
            return WebDriverBy::name($selector);
        }
        $element = $this->getElementByWebDriver(WebDriverBy::className($selector));
        if ($element) {
            return WebDriverBy::className($selector);
        }
        $element = $this->getElementByWebDriver(WebDriverBy::linkText($selector));
        if ($element) {
            return WebDriverBy::linkText($selector);
        }
        $element = $this->getElementByWebDriver(WebDriverBy::xpath($selector));
        if ($element) {
            return WebDriverBy::xpath($selector);
        }
        $element = $this->getElementByWebDriver(WebDriverBy::tagName($selector));
        if ($element) {
            return WebDriverBy::tagName($selector);
        }

        return null;
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
                    if (!$element->isEnabled() || !$element->isDisplayed()) {
                        ++$i;
                        sleep(1);
                    } else {
                        $element->clear();
                        $element->sendKeys($value);
                        return;
                    }
                }
                ++$i;
                sleep(1);
            } catch (\Facebook\WebDriver\Exception\InvalidElementStateException $e) {
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
                        //dump($select->isSelected());
                        //dump($value);
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
    public function checkboxIsChecked($selector, $timeout = self::TIMEOUT)
    {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $select = $this->findField($selector);
                if ($select) {
                    return $select->isSelected();
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
                if (!$button) {
                    ++$i;
                    sleep(1);
                    continue;
                }
                $this->facebookDriver->wait(10)->until(WebDriverExpectedCondition::elementToBeClickable($this->getWebDriverBy($selector)));
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
    public function elementIsVisible($selector, $timeout = self::TIMEOUT)
    {
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $element = $this->getElementBySelector($selector);
                if ($element) {
                    return $element->isDisplayed();
                } else {
                    ++$i;
                    sleep(1);
                }
                $this->ajaxWait();
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }

        return false;
    }
    public function clickElement($selector, $timeout = self::TIMEOUT)
    {
        $e = new \Exception("Impossibile trovare " . $selector);
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $element = $this->getElementBySelector($selector);
                if (!$element || !$this->elementIsVisible($selector)) {
                    ++$i;
                    sleep(1);
                    continue;
                }
                $this->facebookDriver->wait(10)->until(WebDriverExpectedCondition::elementToBeClickable($this->getWebDriverBy($selector)));
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
                $element = $this->facebookDriver->findElement($selector);
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
        $i = 0;
        while ($i < $timeout) {
            try {
                $this->ajaxWait();
                $element = $this->findField($selector);
                if (!$element) {
                    ++$i;
                    sleep(1);
                    continue;
                }
                $this->facebookDriver->
                        action()->
                        contextClick($element)->
                        sendKeys(NULL, WebDriverKeys::ARROW_DOWN)->
                        perform();

                $this->ajaxWait();
                return true;
            } catch (\Exception $e) {
                ++$i;
                sleep(1);
            }
        }

        $this->screenShot();
        return null;
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
    public function tearDown() : void
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

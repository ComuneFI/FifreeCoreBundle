<?php

namespace Fi\PannelloAmministrazioneBundle\Tests\Controller;

use Fi\CoreBundle\DependencyInjection\FifreeTest;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Symfony\Component\Filesystem\Filesystem;

class FifreecoreAdminpanelControllerTest extends FifreeTest
{

    protected static $application;
    protected static $environment = 'test';
    protected static $debug = false;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->setClassName(get_class());
        $kernel = new \AppKernel(static::$environment, static::$debug);
        $kernel->boot();
    }

    public function test1starttest()
    {
        startTests();
    }

    public function test10AdminpanelHomepage()
    {
        //.' --env '.$this->getContainer()->get( 'kernel' )->getEnvironment()
        //$this->cleanFilesystem();
        $this->restartKernel();
        $client = $this->getClientAutorizzato();
        //$url = $client->getContainer()->get('router')->generate('Ffprincipale');
        $url = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_homepage'/* , array('parms' => 'value') */);

        $client->request('GET', $url);
        $this->assertTrue(
                $client->getResponse()->headers->contains('Content-Type', 'text/html; charset=UTF-8')
        );
    }

    /*
     * @test
     */

    public function test20AdminpanelGenerateBundle()
    {
        $this->restartKernel();
        $browser = 'firefox';
        $urlRouting = $this->getContainer()->get('router')->generate('fi_pannello_amministrazione_homepage');
        $url = 'http://127.0.0.1:8000/app_test.php' . $urlRouting;

        // Choose a Mink driver. More about it in later chapters.
        $driver = new \Behat\Mink\Driver\Selenium2Driver($browser);
        $session = new Session($driver);
        // start the session
        $session->start();
        $session->visit($url);
        $page = $session->getPage();
        sleep(1);
        /* Login */
        $page->fillField('username', 'admin');
        $page->fillField('password', 'admin');
        $page->pressButton('_submit');

        sleep(1);

        $page->fillField('bundlename', 'Fi/ProvaBundle');

        $javascript = "window.alert = function() {};";
        $session->executeScript($javascript);
        $page->pressButton('adminpanelgeneratebundle');
        parent::ajaxWait($session, 30000);
        $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
        $session->executeScript($scriptrun);
        parent::ajaxWait($session, 60000);
        //$session->getDriver()->getWebDriverSession()->accept_alert();
        echo $session->getPage()->getHtml();
        parent::ajaxWait($session, 30000);
        //$scriptclose = 'function(){ if ($("#risultato\").is(":visible")) { $("#risultato").dialog("close");}}()';
        $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
        $session->executeScript($scriptclose);

        $session->stop();
        removecache();
    }

    public function test30AdminpanelGenerateEntity()
    {
        $this->restartKernel();
        //$fs = new Filesystem();
        //$fs->remove($this->getContainer()->getParameter('kernel.cache_dir'));
        $browser = 'firefox';
        //$url = $client->getContainer()->get('router')->generate('Ffprincipale');
        $urlRouting = $this->getContainer()->get('router')->generate('fi_pannello_amministrazione_homepage');
        $url = 'http://127.0.0.1:8000/app_test.php' . $urlRouting;

        // Choose a Mink driver. More about it in later chapters.
        $driver = new \Behat\Mink\Driver\Selenium2Driver($browser);
        $session = new Session($driver);
        // start the session
        $session->start();
        $session->visit($url);
        $page = $session->getPage();
        sleep(1);
        // Login
        $page->fillField('username', 'admin');
        $page->fillField('password', 'admin');
        $page->pressButton('_submit');

        sleep(3);
        $page->fillField('bundlename', 'Fi/ProvaBundle');

        $page->selectFieldOption('entitybundle', 'Fi/ProvaBundle');
        $page->selectFieldOption('entityfile', 'wbadmintest.mwb');

        $page->pressButton('adminpanelgenerateentity');
        $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
        $session->executeScript($scriptrun);
        parent::ajaxWait($session, 30000);
        sleep(2);
        //$scriptclose = "function(){ if ($(\"#risultato\").is(\":visible\")) {$(\"#risultato\").dialog(\"close\");}}()";
        $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
        $session->executeScript($scriptclose);

        $page->pressButton('adminpanelaggiornadatabase');
        $scriptdb = "function(){ $('button:contains(\"Si\")').click();}()";
        $session->executeScript($scriptdb);
        parent::ajaxWait($session, 30000);
        sleep(2);
        $session->executeScript($scriptclose);

        //$this->generateentities();
        //$this->clearcache();
        $session->stop();
        removecache();
    }

    public function test40AdminpanelGenerateForm()
    {
        $this->restartKernel();
        //$fs = new Filesystem();
        //$fs->remove($this->getContainer()->getParameter('kernel.cache_dir'));
        $browser = 'firefox';
        //$url = $client->getContainer()->get('router')->generate('Ffprincipale');
        $urlRouting = $this->getContainer()->get('router')->generate('fi_pannello_amministrazione_homepage');
        $url = 'http://127.0.0.1:8000/app_test.php' . $urlRouting;

        // Choose a Mink driver. More about it in later chapters.
        $driver = new \Behat\Mink\Driver\Selenium2Driver($browser);
        $session = new Session($driver);
        // start the session
        $session->start();
        $session->visit($url);
        $page = $session->getPage();
        sleep(1);
        // Login
        $page->fillField('username', 'admin');
        $page->fillField('password', 'admin');
        $page->pressButton('_submit');

        sleep(3);
        $page->fillField('bundlename', 'Fi/ProvaBundle');
        $page->fillField('entityform', 'Prova');

        $page->pressButton('adminpanelgenerateformcrud');
        $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
        $session->executeScript($scriptrun);
        parent::ajaxWait($session, 30000);
        sleep(1);

        //$scriptclose = "function(){ if ($(\"#risultato\").is(\":visible\")) {$(\"#risultato\").dialog(\"close\");}}()";
        $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
        $session->executeScript($scriptclose);
        sleep(2);
        //$this->generateentities();
        $session->stop();
        removecache();
    }

    /**
     * @test
     */
    public function test50AdminpanelTest()
    {
        $this->restartKernel();
        $browser = 'firefox';
        $urlRouting = "/Prova";
        $url = 'http://127.0.0.1:8000/app_test.php' . $urlRouting;

        // Choose a Mink driver. More about it in later chapters.
        $driver = new \Behat\Mink\Driver\Selenium2Driver($browser);
        $session = new Session($driver);
        // start the session
        $session->start();
        $session->visit($url);
        $page = $session->getPage();

        var_dump($page->getHtml());
        sleep(1);
        // Login
        $page->fillField('username', 'admin');
        $page->fillField('password', 'admin');
        $page->pressButton('_submit');

        sleep(3);
        $this->crudoperation($session, $page);

        $session->stop();
        removecache();
    }

    /*
     * @test
     */

    public function test100PannelloAmministrazioneMain()
    {
        $container = $this->getContainer();
        /* @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /* @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');
        $username4test = $container->getParameter('user4test');
        $user = $userManager->findUserBy(array('username' => $username4test));
        $loginManager->loginUser($firewallName, $user);

        /* save the login token into the session and put it in a cookie */
        $container->get('session')->set('_security_' . $firewallName, serialize($container->get('security.token_storage')->getToken()));
        $container->get('session')->save();
    }

    private function crudoperation($session, $page)
    {
        $elementadd = $page->findAll('css', '.ui-icon-plus');

        foreach ($elementadd as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        /* Inserimento */
        parent::ajaxWait($session, 20000);
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        $page->fillField('fi_provabundle_prova_descrizione', $descrizionetest1);
        $page->find('css', 'a#sDataProvaS')->click();
        parent::ajaxWait($session, 20000);

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}()');

        $elementmod = $page->findAll('css', '.ui-icon-pencil');

        foreach ($elementmod as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        /* Modifica */
        $descrizionetest2 = 'Test inserimento descrizione automatico 2';
        $page->fillField('fi_provabundle_prova_descrizione', $descrizionetest2);
        $page->find('css', 'a#sDataProvaS')->click();
        parent::ajaxWait($session);
        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}()');

        $elementdel = $page->findAll('css', '.ui-icon-trash');
        parent::ajaxWait($session, 20000);

        foreach ($elementdel as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $page->find('css', 'a#dData')->click();
        parent::ajaxWait($session, 20000);
    }

    public function testZ9999999999PannelloAmministrazioneMain()
    {
        startTests();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

}

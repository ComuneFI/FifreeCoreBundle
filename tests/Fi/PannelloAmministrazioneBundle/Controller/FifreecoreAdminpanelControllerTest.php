<?php

namespace Fi\PannelloAmministrazioneBundle\Tests\Controller;

use Tests\CoreBundle\Mink\CoreMink;

class FifreecoreAdminpanelControllerTest extends CoreMink
{

    public static function setUpBeforeClass()
    {
        cleanFilesystem();
        clearcache();
    }

    /*
     * @test
     */

    public function test20AdminpanelGenerateBundle()
    {
        //passthru("php " . __DIR__ . '/../../../bin/console' . " cache:clear --no-warmup --env=test ");
        //sleep(2);
        //url da testare
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($this->container);
        $fileprovabundle = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle";
        $checkentityprova = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . "Prova.php";
        $checkresourceprova = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR .
                "doctrine" . DIRECTORY_SEPARATOR . "Prova.orm.yml";
        $checktypeprova = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Form" . DIRECTORY_SEPARATOR . "ProvaType.php";
        $checkviewsprova = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "Prova";
        $checkindexprova = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "Prova" .
                DIRECTORY_SEPARATOR . "index.html.twig";

        $url = $this->router->generate('fi_pannello_amministrazione_homepage');
        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();
        
        sleep(1);
        
        $page->fillField('bundlename', 'Fi/ProvaBundle');

        $javascript = "window.alert = function() {};";
        $session->executeScript($javascript);
        $page->pressButton('adminpanelgeneratebundle');
        $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
        $session->executeScript($scriptrun);
        parent::ajaxWait($session, 60000);
        
        $scriptrun = "function(){ $('button:contains(\"Chiudi\")').click();}()";
        $session->executeScript($scriptrun);
        //parent::ajaxWait($session, 60000);
        //$session->getDriver()->getWebDriverSession()->accept_alert();
        sleep(1);
        //echo $session->getPage()->getHtml();
        /**/
        //$screenshot = $driver->getWebDriverSession()->screenshot();
        //file_put_contents('/tmp/test1.png', base64_decode($screenshot));
        /**/
        //$scriptclose = 'function(){ if ($("#risultato\").is(":visible")) { $("#risultato").dialog("close");}}()';
        //$scriptclose = 'function(){ $("#risultato").dialog("close");}()';
        //$session->executeScript($scriptclose);
        //echo passthru("php " . __DIR__ . '/../../../bin/console' . " cache:clear --no-debug --env=test ");
        /* qui */
        //removecache();
        //clearcache();
        //$driver->reload();

        /* $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
          $session->executeScript($scriptclose); */

        //***************************************************************************************************************
        //$urlRouting = $this->getClientAutorizzato()->getContainer()->get('router')->generate('fi_pannello_amministrazione_homepage');
        //$url = $_ENV['HTTP_TEST_HOST'] . $_ENV['HTTP_TEST_URL'] . $urlRouting;
        //$page->fillField('username', 'admin');
        //$page->fillField('password', 'admin');
        //$page->pressButton('_submit');

        $this->visit($url);
        $checkprovabundle = file_exists($fileprovabundle);
        $this->assertTrue($checkprovabundle, $fileprovabundle);

        sleep(1);
        $page->fillField('bundlename', 'Fi/ProvaBundle');

        $page->selectFieldOption('entitybundle', 'Fi/ProvaBundle');
        $page->selectFieldOption('entityfile', 'wbadmintest.mwb');

        $page->pressButton('adminpanelgenerateentity');
        $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
        $session->executeScript($scriptrun);
        parent::ajaxWait($session, 120000);

        sleep(1);
        $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
        $session->executeScript($scriptclose);

        sleep(1);
        $page->pressButton('adminpanelgenerateclassentity');
        $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
        $session->executeScript($scriptrun);
        parent::ajaxWait($session, 120000);
        sleep(1);
        //echo $session->getPage()->getHtml();
        /**/
        //$screenshot = $driver->getWebDriverSession()->screenshot();
        //file_put_contents('/tmp/test3.png', base64_decode($screenshot));
        /**/
        $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
        $session->executeScript($scriptclose);
        $this->assertTrue(file_exists($checkentityprova));

        $this->assertTrue(file_exists($checkresourceprova));

        /* $page->pressButton('adminpanelcc');
          $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
          $session->executeScript($scriptrun);
          parent::ajaxWait($session, 60000);
          sleep(1); */
        //echo $session->getPage()->getHtml(); 
        /**/
        //$screenshot = $driver->getWebDriverSession()->screenshot();
        //file_put_contents('/tmp/test4.png', base64_decode($screenshot));
        /**/
        //$scriptclose = "function(){ if ($(\"#risultato\").is(\":visible\")) {$(\"#risultato\").dialog(\"close\");}}()";
        /* $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
          $session->executeScript($scriptclose); */

        /* qui */
        //removecache();
        //clearcache();
        //$driver->reload();
        $this->visit($url);

        sleep(1);

        $page->pressButton('adminpanelaggiornadatabase');
        $scriptdb = "function(){ $('button:contains(\"Si\")').click();}()";
        $session->executeScript($scriptdb);
        parent::ajaxWait($session, 60000);
        /**/
        //$screenshot = $driver->getWebDriverSession()->screenshot();
        //file_put_contents('/tmp/test5.png', base64_decode($screenshot));
        /**/

        //echo $session->getPage()->getHtml();
        //$scriptclose = "function(){ if ($(\"#risultato\").is(\":visible\")) {$(\"#risultato\").dialog(\"close\");}}()";
        $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
        $session->executeScript($scriptclose);
        //passthru("php " . __DIR__ . '/../../../bin/console' . " cache:clear --no-debug --env=test ");
        sleep(1);
        /* $session->visit($url);
          $page = $session->getPage();
          sleep(1);
          //echo $session->getPage()->getHtml();
          //Login
          $page->fillField('username', 'admin');
          $page->fillField('password', 'admin');
          $page->pressButton('_submit');

          sleep(1);
          $session->visit($url);
          $page = $session->getPage();

          sleep(1); */
        $page->fillField('bundlename', 'Fi/ProvaBundle');
        $page->fillField('entityform', 'Prova');

        $page->pressButton('adminpanelgenerateformcrud');
        $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
        $session->executeScript($scriptrun);
        parent::ajaxWait($session, 60000);
        sleep(1);
        $this->assertTrue(file_exists($checktypeprova));
        $this->assertTrue(file_exists($checkviewsprova));
        $this->assertTrue(file_exists($checkindexprova));
        /**/
        //$screenshot = $driver->getWebDriverSession()->screenshot();
        //file_put_contents('/tmp/test6.png', base64_decode($screenshot));
        /**/

        sleep(1);

        //echo $session->getPage()->getHtml();
        //$scriptclose = "function(){ if ($(\"#risultato\").is(\":visible\")) {$(\"#risultato\").dialog(\"close\");}}()";
        $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
        $session->executeScript($scriptclose);

        /* $page->pressButton('adminpanelcc');
          $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
          $session->executeScript($scriptrun);
          parent::ajaxWait($session, 60000); */
        //echo passthru("php " . __DIR__ . '/../../../bin/console' . " cache:clear --no-debug --env=test ");

        /* qui */
        //removecache();
        //clearcache();
        //$driver->reload();
        sleep(1);
        //***************************************************************************************************************
        try {
            $urlRouting = $this->router->generate('Prova_container');
        } catch (\Exception $exc) {
            $urlRouting = "/Prova";
        }

        $url = $urlRouting;


        $this->visit($url);
        $page = $session->getPage();

        //echo $page->getHtml();
        sleep(1);
        $this->crudoperation($session, $page);

        sleep(1);
        $session->stop();
    }

    /*
     * @test
     */

    /* public function test100PannelloAmministrazioneMain()
      {
      $container = $this->getClientAutorizzato()->getContainer();
      // @var $userManager \FOS\UserBundle\Doctrine\UserManager
      $userManager = $container->get('fifree.fos_user.user_manager');
      // @var $loginManager \FOS\UserBundle\Security\LoginManager
      $loginManager = $container->get('fifree.fos_user.security.login_manager');
      $firewallName = $container->getParameter('fos_user.firewall_name');
      $username4test = $container->getParameter('user4test');
      $user = $userManager->findUserBy(array('username' => $username4test));
      $loginManager->loginUser($firewallName, $user);

      // save the login token into the session and put it in a cookie
      $container->get('session')->set('_security_' . $firewallName, serialize($container->get('security.token_storage')->getToken()));
      $container->get('session')->save();
      } */

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
//        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
//            $fieldhtml = 'prova_descrizione';
//        } else {
        $fieldhtml = 'fi_provabundle_prova_descrizione';
//        }

        $page->fillField($fieldhtml, $descrizionetest1);
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
        $page->fillField($fieldhtml, $descrizionetest2);
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

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        cleanFilesystem();
        clearcache();
        parent::tearDown();
    }

}

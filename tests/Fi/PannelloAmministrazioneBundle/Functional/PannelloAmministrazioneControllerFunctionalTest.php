<?php

use Tests\CoreBundle\FacebookDriver\FacebookDriverTester;

class PannelloAmministrazioneControllerFunctionalTest extends FacebookDriverTester
{

    public static function setUpBeforeClass() : void
    {
        cleanFilesystem();
        removecache();
        clearcache();
    }

    /*
     * @test
     */

    public function test20AdminpanelGenerateBundle()
    {
        //passthru("php " . __DIR__ . '/../../../bin/console' . " cache:clear --no-warmup --env=test ");
        //
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



        $this->fillField('bundlename', 'Fi/ProvaBundle');

        $javascript = "window.alert = function() {};";
        $this->executeScript($javascript);
        $this->pressButton('adminpanelgeneratebundle');

        $this->pressButton('yesdialogbutton');
        //$scriptrun = "function(){ $('button:contains(\"Si\")').click();";
        //$this->executeScript($scriptrun);
        $this->ajaxWait();

        $scriptrun = "$('button:contains(\"Chiudi\")').click();";
        $this->executeScript($scriptrun);
        //parent::ajaxWait($session, 60000);
        //$session->getDriver()->getWebDriverSession()->accept_alert();
        //echo $session->getPage()->getHtml();
        /**/
        //$screenshot = $driver->getWebDriverSession()->screenshot();
        //file_put_contents('/tmp/test1.png', base64_decode($screenshot));
        /**/
        //$scriptclose = 'function(){ if ($("#risultato\").is(":visible")) { $("#risultato").dialog("close");}}()';
        //$scriptclose = 'function(){ $("#risultato").dialog("close");}()';
        //$this->executeScript($scriptclose);
        //echo passthru("php " . __DIR__ . '/../../../bin/console' . " cache:clear --no-debug --env=test ");
        /* qui */
        //removecache();
        //clearcache();
        //$driver->reload();

        /* $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
          $this->executeScript($scriptclose); */

        //***************************************************************************************************************
        //$urlRouting = $this->getClientAutorizzato()->getContainer()->get('router')->generate('fi_pannello_amministrazione_homepage');
        //$url = $_ENV['HTTP_TEST_HOST'] . $_ENV['HTTP_TEST_URL'] . $urlRouting;
        //$page->fillField('username', 'admin');
        //$page->fillField('password', 'admin');
        //$this->pressButton('_submit');
        clearcache();

        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();


        $this->visit($url);
        $checkprovabundle = file_exists($fileprovabundle);
        $this->assertTrue($checkprovabundle, $fileprovabundle);


        $this->fillField('bundlename', 'Fi/ProvaBundle');

        $this->selectFieldOption('entitybundle', 'Fi/ProvaBundle');
        $this->selectFieldOption('entityfile', 'wbadmintest.mwb');

        $this->pressButton('adminpanelgenerateentity');

        $this->pressButton('yesdialogbutton');

        /* $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
          $this->executeScript($scriptrun); */
        $this->ajaxWait();


        /* $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
          $this->executeScript($scriptclose); */
        $this->pressButton('closedialogbutton');


        $this->pressButton('adminpanelgenerateclassentity');
        /* $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
          $this->executeScript($scriptrun); */

        $this->pressButton('yesdialogbutton');

        $this->ajaxWait();

        //echo $session->getPage()->getHtml();
        /**/
        //$screenshot = $driver->getWebDriverSession()->screenshot();
        //file_put_contents('/tmp/test3.png', base64_decode($screenshot));
        /**/
        /* $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
          $this->executeScript($scriptclose);
         */
        $this->pressButton('closedialogbutton');

        $this->assertTrue(file_exists($checkentityprova));

        $this->assertTrue(file_exists($checkresourceprova));

        /* $this->pressButton('adminpanelcc');
          $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
          $this->executeScript($scriptrun);
          parent::ajaxWait($session, 60000);
         */
        //echo $session->getPage()->getHtml(); 
        /**/
        //$screenshot = $driver->getWebDriverSession()->screenshot();
        //file_put_contents('/tmp/test4.png', base64_decode($screenshot));
        /**/
        //$scriptclose = "function(){ if ($(\"#risultato\").is(\":visible\")) {$(\"#risultato\").dialog(\"close\");}}()";
        /* $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
          $this->executeScript($scriptclose); */

        /* qui */
        //removecache();
        clearcache();
        //$driver->reload();
        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();


        $this->visit($url);


        $this->pressButton('adminpanelaggiornadatabase');
        /* $scriptdb = "function(){ $('button:contains(\"Si\")').click();}()";
          $this->executeScript($scriptdb);
         */

        $this->pressButton('yesdialogbutton');

        $this->ajaxWait();
        /**/
        //$screenshot = $driver->getWebDriverSession()->screenshot();
        //file_put_contents('/tmp/test5.png', base64_decode($screenshot));
        /**/

        //echo $session->getPage()->getHtml();
        //$scriptclose = "function(){ if ($(\"#risultato\").is(\":visible\")) {$(\"#risultato\").dialog(\"close\");}}()";
        /* $scriptclose = 'function(){ $("#risultato").dialog("close");}()';
          $this->executeScript($scriptclose);
         */
        $this->pressButton('closedialogbutton');

        //passthru("php " . __DIR__ . '/../../../bin/console' . " cache:clear --no-debug --env=test ");

        /* $session->visit($url);
          $page = $session->getPage();

          //echo $session->getPage()->getHtml();
          //Login
          $page->fillField('username', 'admin');
          $page->fillField('password', 'admin');
          $this->pressButton('_submit');


          $session->visit($url);
          $page = $session->getPage();

         */

        clearcache();
        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();



        $this->fillField('bundlename', 'Fi/ProvaBundle');
        $this->fillField('entityform', 'Prova');

        $this->pressButton('adminpanelgenerateformcrud');
        /* $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
          $this->executeScript($scriptrun); */

        $this->pressButton('yesdialogbutton');

        $this->ajaxWait();

        //echo $page->getHtml();
        $this->assertTrue(file_exists($checktypeprova));
        $this->assertTrue(file_exists($checkviewsprova));
        $this->assertTrue(file_exists($checkindexprova));
        /**/
        //$screenshot = $driver->getWebDriverSession()->screenshot();
        //file_put_contents('/tmp/test6.png', base64_decode($screenshot));
        /**/



        //echo $session->getPage()->getHtml();
        //$scriptclose = "function(){ if ($(\"#risultato\").is(\":visible\")) {$(\"#risultato\").dialog(\"close\");}}()";
        /*$scriptclose = 'function(){ $("#risultato").dialog("close");}()';
        $this->executeScript($scriptclose);*/
        $this->pressButton('closedialogbutton');
        

        /* $this->pressButton('adminpanelcc');
          $scriptrun = "function(){ $('button:contains(\"Si\")').click();}()";
          $this->executeScript($scriptrun);
          parent::ajaxWait($session, 60000); */
        //echo passthru("php " . __DIR__ . '/../../../bin/console' . " cache:clear --no-debug --env=test ");

        /* qui */
        //removecache();
        clearcache();
        //$driver->reload();
        //***************************************************************************************************************
        try {
            $urlRouting = $this->router->generate('Prova_container');
        } catch (\Exception $exc) {
            $urlRouting = "/Prova";
        }

        $url = $urlRouting;


        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();



        //echo $page->getHtml();

        $this->crudoperation($session, $page);


        $session->quit();
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
        $this->clickElement('buttonadd_list1');

        /* Inserimento */
        $descrizionetest1 = 'Test inserimento descrizione automatico';
//        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
//            $fieldhtml = 'prova_descrizione';
//        } else {
        $fieldhtml = 'fi_provabundle_prova_descrizione';
//        }

        $this->fillField($fieldhtml, $descrizionetest1);

        $this->clickElement('sDataProvaS');


        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}(testid)');


        $this->clickElement('buttonedit_list1');

        /* Modifica */
        $descrizionetest2 = 'Test inserimento descrizione automatico 2';
        $this->fillField($fieldhtml, $descrizionetest2);

        $this->clickElement('sDataProvaS');

        $this->ajaxWait();
        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}(testid)');
        $this->ajaxWait();


        $this->clickElement('buttondel_list1');

        $this->clickElement('dData');
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown() : void
    {
        parent::tearDown();
        cleanFilesystem();
        removecache();
        clearcache();
    }

}

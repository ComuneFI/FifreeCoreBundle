<?php

use Tests\CoreBundle\FacebookDriver\FacebookDriverTester;

class PannelloAmministrazioneControllerFunctionalTest extends FacebookDriverTester
{

    public static function setUpBeforeClass() : void
    {
        cleanFilesystem();
        databaseinit();
        removecache();
        clearcache();
    }

    /*
     * @test
     */

    public function test20AdminpanelGenerateBundle()
    {
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($this->container);
        $checkentityprova = $apppath->getSrcPath() . "/Entity/Prova.php";
        $checkentitybaseprova = $apppath->getSrcPath() . "/Entity/BaseProva.php";
        $checkentitytabellacollegataprova = $apppath->getSrcPath() . "/Entity/Tabellacollegata.php";
        $checkentitytabellacollegatabaseprova = $apppath->getSrcPath() . "/Entity/BaseTabellacollegata.php";
        $checktypeprova = $apppath->getSrcPath() . "/Form/ProvaType.php";
        $checkviewsprova = $apppath->getSrcPath() . "/../templates/Prova";
        $checkindexprova = $apppath->getSrcPath() . "/../templates/Prova/index.html.twig";

        $this->assertFalse(file_exists($checkentityprova));
        $this->assertFalse(file_exists($checkentitybaseprova));
        $this->assertFalse(file_exists($checkentitytabellacollegataprova));
        $this->assertFalse(file_exists($checkentitytabellacollegatabaseprova));
        $this->assertFalse(file_exists($checktypeprova));
        $this->assertFalse(file_exists($checkviewsprova));
        $this->assertFalse(file_exists($checkindexprova));

        $url = $this->router->generate('fi_pannello_amministrazione_homepage');
        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();

        $this->visit($url);
        $this->selectFieldOption('entityfile', 'wbadmintest.mwb');
        $this->pressButton('adminpanelgenerateentity');
        $this->pressButton('yesdialogbutton');
        $this->ajaxWait();

        //$screenshot = $this->facebookDriver->takeScreenshot();
        //file_put_contents('/tmp/screenshot.txt', base64_encode($screenshot)."\n\n", FILE_APPEND);
        //echo $page->getPageSource();

        $this->pressButton('closedialogbutton');
        $this->assertTrue(file_exists($checkentityprova));
        $this->assertTrue(file_exists($checkentitybaseprova));
        $this->assertTrue(file_exists($checkentitytabellacollegataprova));
        $this->assertTrue(file_exists($checkentitytabellacollegatabaseprova));

        $this->pressButton('adminpanelaggiornadatabase');
        $this->pressButton('yesdialogbutton');
        $this->ajaxWait();

        //$screenshot = $this->facebookDriver->takeScreenshot();
        //file_put_contents('/tmp/screenshot.txt', base64_encode($screenshot)."\n\n", FILE_APPEND);
        //echo $page->getPageSource();

        $this->pressButton('closedialogbutton');

        $this->fillField('entityform', 'Prova');
        $this->pressButton('adminpanelgenerateformcrud');
        $this->pressButton('yesdialogbutton');
        $this->ajaxWait();

        //$screenshot = $this->facebookDriver->takeScreenshot();
        //file_put_contents('/tmp/screenshot.txt', base64_encode($screenshot)."\n\n", FILE_APPEND);
        //echo $page->getPageSource();

        $this->assertTrue(file_exists($checktypeprova));
        $this->assertTrue(file_exists($checkviewsprova));
        $this->assertTrue(file_exists($checkindexprova));

        $this->pressButton('closedialogbutton');

        //***************************************************************************************************************
        try {
            $urlRouting = $this->router->generate('Prova_container');
        } catch (\Exception $exc) {
            $urlRouting = "/Prova";
        }

        $url = $urlRouting;

        $this->visit($url);
        $this->crudoperation($session, $page);

        $session->quit();
    }

    private function crudoperation($session, $page)
    {
        $this->clickElement('buttonadd_list1');

        /* Inserimento */
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        $fieldhtml = 'prova_descrizione';

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

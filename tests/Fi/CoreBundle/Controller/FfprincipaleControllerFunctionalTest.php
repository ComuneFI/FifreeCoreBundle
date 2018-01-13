<?php

namespace Fi\CoreBundle\Controller;

use Fi\CoreBundle\DependencyInjection\FifreeTestUtil;

class FfprincipaleControllerFunctionalTest extends FifreeTestUtil
{
    /*
     * @test
     */

    public function testFfprincipale()
    {
        $url = $_ENV['HTTP_TEST_HOST'] . $_ENV['HTTP_TEST_URL'];
        $loginobj = $this->getMinkLoginPage($url);
        $session = $loginobj["session"];
        $page = $loginobj["page"];
        
        $this->crudoperation($session, $page);

        sleep(1);
        $this->searchoperation($session, $page);

        sleep(1);
        $this->printoperations($session, $page);

        $session->stop();
    }

    private function searchoperation($session, $page)
    {
        $elementsearch = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        /* Ricerca 1 */
        parent::ajaxWait($session, 20000);
        $search1 = 'primo';
        sleep(1);
        $page->fillField('jqg1', $search1);
        $page->find('css', 'a#fbox_list1_search')->click();
        parent::ajaxWait($session, 20000);

        $numrowsgrid1 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(0, $numrowsgrid1);

        /* Ricerca 1 */
        $elementsearch2 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch2 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $search2 = 'primo';
        sleep(1);
        //$page->selectFieldOption('inizia con', "cn");
        $var2 = '"cn"';
        $javascript2 = "$('.selectopts option[value=" . $var2 . "]').attr('selected', 'selected').change();;";

        $session->executeScript($javascript2);
        $page->fillField('jqg1', $search2);

        $page->find('css', 'a#fbox_list1_search')->click();
        parent::ajaxWait($session, 20000);

        $numrowsgrid2 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(1, $numrowsgrid2);
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
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'ffprincipale_';
        } else {
            $fieldprefix = 'fi_corebundle_ffprincipaletype_';
        }
        parent::ajaxWait($session, 20000);
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        $page->fillField($fieldprefix . 'descrizione', $descrizionetest1);
        $page->find('css', 'a#sDataFfprincipaleS')->click();
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
        $page->fillField($fieldprefix . 'descrizione', $descrizionetest2);
        $page->find('css', 'a#sDataFfprincipaleS')->click();
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

    private function printoperations($session, $page)
    {
        /* Print pdf */
        $element = $page->findAll('css', '.ui-icon-print');

        foreach ($element as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $windowNames = $session->getWindowNames();
        if (count($windowNames) > 1) {
            $session->switchToWindow($windowNames[1]);
            $page = $session->getPage();
            sleep(1);
            $element = $page->find('css', '.textLayer');

            if (empty($element)) {
                echo $page->getHtml();
                throw new \Exception("No html element found for the selector 'textLayer'");
            }
            $this->assertContains('FiFree2', $element->getText());
            $this->assertContains('Ffprincipale', $element->getText());
            $this->assertContains('Descrizione primo record', $element->getText());

            $session->executeScript('window.close()');
            $mainwindow = $windowNames[0];
            $session->switchToWindow($mainwindow);
            $page = $session->getPage();
        }
    }

}

<?php

namespace Fi\CoreBundle\Tests\Controller;

use Tests\CoreBundle\Mink\CoreMink;

class FfprincipaleControllerFunctionalTest extends CoreMink
{
    /*
     * @test
     * @covers Fi\CoreBundle\Controller\FiController::<public>
     * @covers Fi\CoreBundle\Controller\FiCoreController::<public>
     */

    public function testFfprincipale()
    {
        //$url = $_ENV['HTTP_TEST_HOST'] . $_ENV['HTTP_TEST_URL'];
        $url = "/Ffprincipale";
        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();

        sleep(1);

        $this->crudoperation($session, $page);

        sleep(1);
        $this->searchoperation($session, $page);

        sleep(1);
        $this->printoperations($session, $page);

        $session->stop();
    }

    private function searchoperation($session, $page)
    {
        sleep(1);
        $this->clickElement('#search_list1');
        /* Ricerca 1 */
        $search1 = 'primo';
        sleep(1);
        $page->fillField('jqg1', $search1);
        $page->find('css', 'a#fbox_list1_search')->click();
        $this->ajaxWait();

        $numrowsgrid1 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(0, $numrowsgrid1);

        /* Ricerca 1 */
        sleep(1);
        $this->clickElement('#search_list1');
        $search2 = 'primo';
        sleep(1);
        //$page->selectFieldOption('inizia con', "cn");
        $var2 = '"cn"';
        $javascript2 = "$('.selectopts option[value=" . $var2 . "]').attr('selected', 'selected').change();;";

        $session->executeScript($javascript2);
        $page->fillField('jqg1', $search2);

        $page->find('css', 'a#fbox_list1_search')->click();
        $this->ajaxWait();

        $numrowsgrid2 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(1, $numrowsgrid2);
    }

    private function crudoperation($session, $page)
    {
        $this->clickElement('#buttonadd_list1');

        /* Inserimento */
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'ffprincipale_';
        } else {
            $fieldprefix = 'fi_corebundle_ffprincipaletype_';
        }
        $this->ajaxWait();
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        $page->fillField($fieldprefix . 'descrizione', $descrizionetest1);
        $page->find('css', 'a#sDataFfprincipaleS')->click();
        $this->ajaxWait();

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}()');

        $this->clickElement('#buttonedit_list1');
        sleep(1);
        /* Modifica */
        $descrizionetest2 = 'Test inserimento descrizione automatico 2';
        $page->fillField($fieldprefix . 'descrizione', $descrizionetest2);
        $page->find('css', 'a#sDataFfprincipaleS')->click();
        $this->ajaxWait();
        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}()');

        sleep(1);
        $this->clickElement('#buttondel_list1');
        sleep(1);
        $this->clickElement('a#dData');
    }

    private function printoperations($session, $page)
    {
        $this->clickElement('#buttonprint_list1');
        $windowNames = $session->getWindowNames();
        if (count($windowNames) > 1) {
            $session->switchToWindow($windowNames[1]);
            $page = $session->getPage();
            sleep(1);
            $element = $page->find('css', '.textLayer');

            if (empty($element)) {
                if (strpos($page->getHtml(), "application/pdf") >= 0) {
                    $this->assertContains("application/pdf", $page->getHtml());
                } else {
                    echo $page->getHtml();
                    throw new \Exception("No html element found for the selector 'textLayer'");
                }
            } else {
                $this->assertContains('FiFree2', $element->getText());
                $this->assertContains('Ffprincipale', $element->getText());
                $this->assertContains('Descrizione primo record', $element->getText());
            }

            $session->executeScript('window.close()');
            $mainwindow = $windowNames[0];
            $session->switchToWindow($mainwindow);
            $page = $session->getPage();
        }
    }

}

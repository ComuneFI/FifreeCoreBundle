<?php

use Tests\CoreBundle\FacebookDriver\FacebookDriverTester;

class FfprincipaleControllerFunctionalTest extends FacebookDriverTester {
    /*
     * @test
     * @covers Fi\CoreBundle\Controller\FiController::<public>
     * @covers Fi\CoreBundle\Controller\FiCoreController::<public>
     */

    public function testFfprincipale() {
        $url = "/Ffprincipale";
        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();

        $this->crudoperation($session);

        $this->searchoperation($session);

        $this->printoperations($session);

        $this->logout();

        $session->quit();
    }

    private function searchoperation($session) {

        $this->clickElement('search_list1');
        /* Ricerca 1 */
        $search1 = 'primo';

        $this->fillField('jqg1', $search1);
        $this->clickElement('fbox_list1_search');
        $this->ajaxWait();

        $numrowsgrid1 = $this->evaluateScript('var numrow = function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}(numrow)');
        $this->assertEquals(0, $numrowsgrid1);

        /* Ricerca 1 */
        $this->clickElement('search_list1');
        $search2 = 'primo';

        $var2 = '"cn"';
        $javascript2 = "$('.selectopts option[value=" . $var2 . "]').attr('selected', 'selected').change();;";

        $this->executeScript($javascript2);
        $this->ajaxWait();
        $this->fillField('jqg1', $search2);

        $this->clickElement('fbox_list1_search');
        $this->ajaxWait();

        $numrowsgrid2 = $this->evaluateScript('return $("#list1").jqGrid("getGridParam", "records");');
        $this->assertEquals(1, $numrowsgrid2);
    }

    private function crudoperation($session) {
        $this->clickElement('buttonadd_list1');

        /* Inserimento */
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'ffprincipale_';
        } else {
            $fieldprefix = 'fi_corebundle_ffprincipaletype_';
        }
        $this->ajaxWait();
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        $this->fillField($fieldprefix . 'descrizione', $descrizionetest1);

        $this->clickElement('sDataFfprincipaleS');

        $this->ajaxWait();

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var rowid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}(rowid)');


        $this->clickElement('buttonedit_list1');

        /* Modifica */
        $descrizionetest2 = 'Test inserimento descrizione automatico 2';
        $this->fillField($fieldprefix . 'descrizione', $descrizionetest2);

        $this->clickElement('sDataFfprincipaleS');

        $this->ajaxWait();
        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var rowid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}(rowid)');
        $this->ajaxWait();


        $this->clickElement('buttondel_list1');

        $this->clickElement('dData');
    }

    private function printoperations($session) {
        $this->clickElement('buttonprint_list1');
        sleep(5);
        $windows = $this->facebookDriver->getWindowHandles();
        $lastwindow = end($windows);
        $this->facebookDriver->switchTo()->window($lastwindow);

        $page = $this->getCurrentPageContent();
        $find = strpos($page, 'name="plugin" id="plugin"');
        $this->assertContains("application/pdf", $page);

        $session->executeScript('window.close();');

        $windows2 = $this->facebookDriver->getWindowHandles();
        $lastwindow2 = end($windows2);
        $this->facebookDriver->switchTo()->window($lastwindow2);
    }

}

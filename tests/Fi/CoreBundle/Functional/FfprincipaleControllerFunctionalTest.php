<?php

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
        $url = "/Ffprincipale";
        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();

        

        $this->crudoperation($session, $page);

        
        $this->searchoperation($session, $page);

        
        $this->printoperations($session, $page);
        
        $this->logout();

        $session->stop();
    }

    private function searchoperation($session, $page)
    {
        
        $this->clickElement('#search_list1');
        /* Ricerca 1 */
        $search1 = 'primo';
        
        $this->fillField('jqg1', $search1);
        $this->clickElement('a#fbox_list1_search');
        $this->ajaxWait();

        $numrowsgrid1 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(0, $numrowsgrid1);

        /* Ricerca 1 */
        $this->clickElement('#search_list1');
        $search2 = 'primo';
        
        $var2 = '"cn"';
        $javascript2 = "$('.selectopts option[value=" . $var2 . "]').attr('selected', 'selected').change();;";

        $session->executeScript($javascript2);
        $this->fillField('jqg1', $search2);

        $this->clickElement('a#fbox_list1_search');
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
        $this->fillField($fieldprefix . 'descrizione', $descrizionetest1);
        
        $this->clickElement('a#sDataFfprincipaleS');
        
        $this->ajaxWait();

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}()');

        
        $this->clickElement('#buttonedit_list1');
        
        /* Modifica */
        $descrizionetest2 = 'Test inserimento descrizione automatico 2';
        $this->fillField($fieldprefix . 'descrizione', $descrizionetest2);
        
        $this->clickElement('#sDataFfprincipaleS');
        
        $this->ajaxWait();
        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}()');
        $this->ajaxWait();

        
        $this->clickElement('#buttondel_list1');
        
        $this->clickElement('a#dData');
    }

    private function printoperations($session, $page)
    {
        $this->clickElement('#buttonprint_list1');
        $windowNames = $session->getWindowNames();
        if (count($windowNames) > 1) {
            $session->switchToWindow($windowNames[1]);
            $pagepdf = $session->getPage();
            
            sleep(2);
            $element = $pagepdf->find('css', '.textLayer');

            if (empty($element)) {
                if (strpos($pagepdf->getHtml(), "application/pdf") >= 0) {
                    $this->assertContains("application/pdf", $pagepdf->getHtml());
                } else {
                    echo $pagepdf->getHtml();
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

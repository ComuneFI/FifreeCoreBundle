<?php

namespace Fi\CoreBundle\Tests\Controller;

use Tests\CoreBundle\Mink\CoreMink;

class FfsecondariaControllerFunctionalTest extends CoreMink
{
    /*
     * @test
     * @covers Fi\CoreBundle\Controller\StoricomodificheController::<public>
     * @covers Fi\CoreBundle\Entity\StoricomodificheRepository::<public>
     * @covers Fi\CoreBundle\Controller\FiCoreController::<public>
     * @covers Fi\CoreBundle\Controller\FiController::<public>
     */

    public function testFfsecondaria()
    {
        //$url = $_ENV['HTTP_TEST_HOST'] . $_ENV['HTTP_TEST_URL'] . $this->router->generate('Ffsecondaria');
        $url = "/Ffsecondaria";
        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();

        sleep(1);

        $this->configuratabelleoperation($session, $page);

        $this->validationoperation($session, $page);

        $this->searchoperation($session, $page);

        $this->crudoperation($session, $page);

        $this->printoperations($session, $page);

        $session->stop();
    }

    private function crudoperation($session, $page)
    {
        $this->clickElement('#buttonadd_list1');
        sleep(2);
        /* Inserimento */
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'ffsecondaria_';
        } else {
            $fieldprefix = 'fi_corebundle_ffsecondariatype_';
        }
        sleep(1);
        $page->fillField($fieldprefix . 'descsec', $descrizionetest1);
        $page->selectFieldOption($fieldprefix . 'ffprincipale', 1);
        $page->selectFieldOption($fieldprefix . 'data_day', (int) date('d'));
        $page->selectFieldOption($fieldprefix . 'data_month', (int) date('m'));
        $page->selectFieldOption($fieldprefix . 'data_year', (int) date('Y'));
        $page->fillField($fieldprefix . 'importo', 1000000.12);
        $page->fillField($fieldprefix . 'intero', 1000000);
        $page->fillField($fieldprefix . 'nota', 'Prova la nota');
        $page->fillField($fieldprefix . 'attivo', 1);

        $page->find('css', 'a#sDataFfsecondariaS')->click();
        $this->ajaxWait();
        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}()');
        $this->clickElement('#buttonedit_list1');
        /* Modifica */
        $descrizionetest2 = 'Test inserimento descrizione automatico 2';
        $page->fillField($fieldprefix . 'descsec', $descrizionetest2);
        $page->find('css', 'a#sDataFfsecondariaS')->click();
        $this->ajaxWait();

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}()');
        $this->clickElement('#buttonedit_list1');

        $selector = $fieldprefix . 'descsec';
        $element = $page->find('css', "#" . $selector);

        if (empty($element)) {
            throw new \Exception("No html element found for the selector ('$selector')");
        }

        $element->rightClick();

        sleep(1);
        $elementmodifiche = $page->find('css', "div#jqContextMenu");

        if (empty($element)) {
            throw new \Exception("No html element found for the selector ('jqContextMenu')");
        }
        $elementmodifiche->click();
        sleep(1);
        $page->pressButton('Ok');

        $elementchiudi = $page->find('css', ".fi-default-chiudi");

        if (empty($element)) {
            throw new \Exception("No html element found for the selector ('jqContextMenu')");
        }

        $elementchiudi->click();

        $this->searchmodifiche($descrizionetest1);
        /* Cancellazione */
        $jsSetFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSetFirstRow . '}()');
        $this->clickElement('#buttondel_list1');
        $this->clickElement('a#dData');
    }

    private function configuratabelleoperation($session, $page)
    {
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'ffsecondaria_';
        } else {
            $fieldprefix = 'fi_corebundle_ffsecondariatype_';
        }
        /**/
        $this->clickElement('#buttonconfig_list1');
        $jsSetFirstRow = '$("#listconfigura").jqGrid("setSelection", rowidcal);';
        $session->evaluateScript('function(){ var rowidcal = $($("#listconfigura").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSetFirstRow . '}()');
        $this->ajaxWait();
        sleep(1);

        $selector = 'input[name=ordineindex]';
        $element = $page->find('css', $selector);

        if (empty($element)) {
            echo $page->getHtml();
            throw new \Exception("No html element found for the selector ('$selector')");
        }

        $element->doubleClick();
        $script = 'function(){$("input[name=mostraindex]").prop("checked", false);}()';
        $session->evaluateScript($script);

        //$script = 'function(){$("#listconfigura").trigger("keydown", {which: 50});}()';
        //$session->evaluateScript($script);
        $jsSaveFirstRow = '$("#listconfigura").saveRow(rowidcalsave);';
        $session->evaluateScript('function(){ var rowidcalsave = $($("#listconfigura").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSaveFirstRow . '}()');
        $this->ajaxWait();

        $selector = '.ui-icon-circle-close';
        $element = $page->find('css', $selector);

        if (empty($element)) {
            throw new \Exception("No html element found for the selector ('$selector')");
        }

        $element->click();

        $elementattivo = $page->findAll('css', '#jqgh_list1_attivo');
        foreach ($elementattivo as $e) {
            $this->assertTrue(!$e->isVisible());
        }

        /**/
        sleep(1);
        $this->clickElement('#buttonconfig_list1');
        $jsSetFirstRow = '$("#listconfigura").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#listconfigura").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSetFirstRow . '}()');
        $this->ajaxWait();
        sleep(1);
        $selector = 'input[name=ordineindex]';
        $element = $page->find('css', $selector);

        if (empty($element)) {
            throw new \Exception("No html element found for the selector ('$selector')");
        }

        $element->doubleClick();
        $script = 'function(){$("input[name=mostraindex]").prop("checked", true);}()';
        $session->evaluateScript($script);

        //$script = 'function(){$("#listconfigura").trigger("keydown", {which: 50});}()';
        //$session->evaluateScript($script);

        $jsSaveFirstRow = '$("#listconfigura").saveRow(rowidcalsave);';
        $session->evaluateScript('function(){ var rowidcalsave = $($("#listconfigura").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSaveFirstRow . '}()');
        $this->ajaxWait();

        $selector = '.ui-icon-circle-close';
        $element = $page->find('css', $selector);

        if (empty($element)) {
            throw new \Exception("No html element found for the selector ('$selector')");
        }

        $element->click();

        $elementattivo = $page->findAll('css', '#jqgh_list1_attivo');
        foreach ($elementattivo as $e) {
            $this->assertTrue($e->isVisible());
        }
    }

    private function validationoperation($session, $page)
    {
        $this->clickElement('#buttonadd_list1');
        /* Inserimento */
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'ffsecondaria_';
        } else {
            $fieldprefix = 'fi_corebundle_ffsecondariatype_';
        }
        $page->fillField($fieldprefix . 'descsec', $descrizionetest1);
        $page->selectFieldOption($fieldprefix . 'ffprincipale', 1);
        $page->selectFieldOption($fieldprefix . 'data_day', (int) date('d'));
        $page->selectFieldOption($fieldprefix . 'data_month', (int) date('m'));
        $page->selectFieldOption($fieldprefix . 'data_year', (int) date('Y'));
        $page->fillField($fieldprefix . 'importo', 1);
        $page->fillField($fieldprefix . 'intero', 1);
        $page->fillField($fieldprefix . 'nota', 'Prova la nota validation');
        $page->fillField($fieldprefix . 'attivo', 0);

        $page->find('css', 'a#sDataFfsecondariaS')->click();
        $this->ajaxWait();
        $elementvalid = $page->findAll('css', '.error_list');

        foreach ($elementvalid as $e) {
            $this->assertTrue($e->isVisible());
        }

        $page->fillField($fieldprefix . 'importo', 2);
        $page->fillField($fieldprefix . 'intero', 2);
        $page->find('css', 'a#sDataFfsecondariaS')->click();
        $this->ajaxWait();

        /* Cancellazione */
        $jsSetFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSetFirstRow . '}()');
        $this->clickElement('#buttondel_list1');
        $page->find('css', 'a#dData')->click();
        $this->ajaxWait();
    }

    private function searchoperation($session, $page)
    {
        /* Ricerca 0 */
        $this->clickElement('#search_list1');
        sleep(1);
        $search0 = "10° secondaria legato al 2° record principale ed è l'";
        $page->fillField('jqg1', $search0);
        $page->find('css', 'a#fbox_list1_search')->click();
        $this->ajaxWait();
        sleep(1);

        $numrowsgrid0 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(1, $numrowsgrid0);
        /* Ricerca 0 */
        
        $this->clickElement('#search_list1');
        $search0 = "l'";
        $var2 = '"cn"';
        $javascript2 = "$('.selectopts option[value=" . $var2 . "]').attr('selected', 'selected').change();;";

        $session->executeScript($javascript2);
        $page->fillField('jqg1', $search0);
        $this->ajaxWait();

        $page->find('css', 'a#fbox_list1_search')->click();
        $this->ajaxWait();

        $numrowsgrid0 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(1, $numrowsgrid0);

        /* Ricerca 0 */

        /* Ricerca 1 */
        $this->clickElement('#search_list1');
        $search1 = '9° secondaria';
        $page->fillField('jqg1', $search1);
        $page->find('css', 'a#fbox_list1_search')->click();
        $this->ajaxWait();
        sleep(1);

        $numrowsgrid1 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(1, $numrowsgrid1);

        /* Ricerca 1 */
        $this->clickElement('#search_list1');
        $search2 = '1°';
        //$page->selectFieldOption('inizia con', "cn");
        $var2 = '"cn"';
        $javascript2 = "$('.selectopts option[value=" . $var2 . "]').attr('selected', 'selected').change();;";

        $session->executeScript($javascript2);
        $page->fillField('jqg1', $search2);
        $this->ajaxWait();

        $page->find('css', 'a#fbox_list1_search')->click();
        $this->ajaxWait();

        $numrowsgrid2 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(5, $numrowsgrid2);
        $this->ajaxWait();
        sleep(1);

        /* doppia condizione */
        $this->clickElement('#search_list1');
        $search3 = 100;
        $addrulejs = "$('.ui-add').click();";
        $this->ajaxWait();

        $session->executeScript($addrulejs);
        $this->ajaxWait();

        $var3 = '"intero"';
        $selector3 = '#fbox_list1.searchFilter table.group.ui-widget.ui-widget-content tbody tr td.columns select:first';
        $javascript3 = "$('" . $selector3 . ' option[value=' . $var3 . "]').attr('selected', 'selected').change();";
        $this->ajaxWait();
        $session->executeScript($javascript3);
        $this->ajaxWait();
        $page->fillField('jqg4', $search3);

        $var4 = '"ge"';
        $javascript4 = "$('.selectopts:first option[value=" . $var4 . "]').attr('selected', 'selected').change();;";
        $session->executeScript($javascript4);
        $search5 = '6°';
        $page->fillField('jqg3', $search5);

        $page->find('css', 'a#fbox_list1_search')->click();
        $this->ajaxWait();

        $numrowsgrid3 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(1, $numrowsgrid3);

        //reset filtri
        $this->clickElement('#search_list1');
        $page->find('css', 'a#fbox_list1_reset')->click();
        sleep(1);


        /* Ricerca 4 */
        $this->clickElement('#search_list1');
        /**/
        $var5 = '"attivo"';
        $selector5 = '#fbox_list1.searchFilter table.group.ui-widget.ui-widget-content tbody tr td.columns select:first';
        $javascript5 = "$('" . $selector5 . ' option[value=' . $var5 . "]').attr('selected', 'selected').change();";
        $this->ajaxWait();
        $session->executeScript($javascript5);
        $this->ajaxWait();
        /**/

        $page->find('css', 'a#fbox_list1_search')->click();
        $this->ajaxWait();

        $numrowsgrid5 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(9, $numrowsgrid5);
        $this->ajaxWait();
        sleep(1);

        /* Ricerca 5 */
        $this->clickElement('#search_list1');
        /**/
        $var6 = '"true"';
        $selector6 = '.input-elm';
        $javascript6 = "$('" . $selector6 . ' option[value=' . $var6 . "]').attr('selected', 'selected').change();";
        $this->ajaxWait();
        $session->executeScript($javascript6);
        $this->ajaxWait();
        /**/

        $page->find('css', 'a#fbox_list1_search')->click();
        $this->ajaxWait();

        $numrowsgrid5 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(6, $numrowsgrid5);
        $this->ajaxWait();
        sleep(1);

        /* Ricerca 6 */
        $this->clickElement('#search_list1');

        $var6 = '"false"';
        $selector6 = '.input-elm';
        $javascript6 = "$('" . $selector6 . ' option[value=' . $var6 . "]').attr('selected', 'selected').change();";
        $this->ajaxWait();
        $session->executeScript($javascript6);
        $this->ajaxWait();
        /**/

        $page->find('css', 'a#fbox_list1_search')->click();
        $this->ajaxWait();

        $numrowsgrid5 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(3, $numrowsgrid5);
        $this->ajaxWait();
        sleep(1);

        //reset filtri
        $this->clickElement('#search_list1');
        $page->find('css', 'a#fbox_list1_reset')->click();
        sleep(1);
    }

    private function printoperations($session, $page)
    {
        /* Print pdf */
        $this->clickElement('#buttonprint_list1');
        sleep(1);
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
                $this->assertContains('Ffsecondaria', $element->getText());
                $this->assertContains('Descrizione secondo record', $element->getText());
            }


            sleep(1);
            $session->executeScript('window.close()');
            $mainwindow = $windowNames[0];
            sleep(1);
            $session->switchToWindow($mainwindow);
            sleep(1);
            $page = $session->getPage();
        }
    }

    private function searchmodifiche($valoreprecedente)
    {
        $em = $this->doctrine->getManager();

        $qu = $em->createQueryBuilder();
        $qu->select(array('c'))
                ->from('FiCoreBundle:Storicomodifiche', 'c')
                ->where("c.nometabella= 'Ffsecondaria'")
                ->andWhere("c.nomecampo = 'descsec'");
        $ff = $qu->getQuery()->getResult();
        $this->assertEquals(count($ff), 1);
        $this->assertEquals($ff[0]->getValoreprecedente(), $valoreprecedente);
    }

}

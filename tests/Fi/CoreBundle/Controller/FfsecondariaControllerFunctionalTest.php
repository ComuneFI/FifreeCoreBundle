<?php

namespace Fi\CoreBundle\Controller;

use Fi\CoreBundle\DependencyInjection\FifreeTestUtil;

class FfsecondariaControllerFunctionalTest extends FifreeTestUtil
{
    /*
     * @test
     */

    public function testFfsecondaria()
    {
        $loginobj = $this->getMinkLoginPage();
        $session = $loginobj["session"];
        $page = $loginobj["page"];

        $this->configuratabelleoperation($session, $page);

        $this->validationoperation($session, $page);

        $this->crudoperation($session, $page);

        $this->searchoperation($session, $page);

        $this->printoperations($session, $page);

        $session->stop();
    }

    private function crudoperation($session, $page)
    {
        parent::ajaxWait($session, 20000);

        $elementadd = $page->findAll('css', '.ui-icon-plus');

        foreach ($elementadd as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }

        parent::ajaxWait($session, 20000);
        /* Inserimento */
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        sleep(1);
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
        $page->fillField($fieldprefix . 'importo', 1000000.12);
        $page->fillField($fieldprefix . 'intero', 1000000);
        $page->fillField($fieldprefix . 'nota', 'Prova la nota');
        $page->fillField($fieldprefix . 'attivo', 1);

        $page->find('css', 'a#sDataFfsecondariaS')->click();
        parent::ajaxWait($session);
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
        $page->fillField($fieldprefix . 'descsec', $descrizionetest2);
        $page->find('css', 'a#sDataFfsecondariaS')->click();
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
        $elementdel = $page->findAll('css', '.ui-icon-trash');

        foreach ($elementdel as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        $page->find('css', 'a#dData')->click();
        parent::ajaxWait($session);
    }

    private function configuratabelleoperation($session, $page)
    {
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'ffsecondaria_';
        } else {
            $fieldprefix = 'fi_corebundle_ffsecondariatype_';
        }
        /**/
        $elementcalc = $page->findAll('css', '.ui-icon-calculator');

        foreach ($elementcalc as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $jsSetFirstRow = '$("#listconfigura").jqGrid("setSelection", rowidcal);';
        $session->evaluateScript('function(){ var rowidcal = $($("#listconfigura").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSetFirstRow . '}()');
        parent::ajaxWait($session, 20000);
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
        parent::ajaxWait($session, 20000);

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
        $elementcalc = $page->findAll('css', '.ui-icon-calculator');

        foreach ($elementcalc as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $jsSetFirstRow = '$("#listconfigura").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#listconfigura").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSetFirstRow . '}()');
        parent::ajaxWait($session, 20000);
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
        parent::ajaxWait($session, 20000);

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
        parent::ajaxWait($session, 20000);

        $elementadd = $page->findAll('css', '.ui-icon-plus');

        foreach ($elementadd as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }

        parent::ajaxWait($session, 20000);
        /* Inserimento */
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        sleep(1);
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
        parent::ajaxWait($session);
        $elementvalid = $page->findAll('css', '.error_list');

        foreach ($elementvalid as $e) {
            $this->assertTrue($e->isVisible());
        }

        $page->fillField($fieldprefix . 'importo', 2);
        $page->fillField($fieldprefix . 'intero', 2);
        $page->find('css', 'a#sDataFfsecondariaS')->click();
        parent::ajaxWait($session);

        /* Cancellazione */
        $jsSetFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSetFirstRow . '}()');
        $elementdel = $page->findAll('css', '.ui-icon-trash');

        foreach ($elementdel as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        $page->find('css', 'a#dData')->click();
        parent::ajaxWait($session);
    }

    private function searchoperation($session, $page)
    {
        parent::ajaxWait($session, 20000);

        $elementsearch = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        /* Ricerca 1 */
        parent::ajaxWait($session, 20000);
        $search1 = '9° secondaria';
        $page->fillField('jqg1', $search1);
        $page->find('css', 'a#fbox_list1_search')->click();
        parent::ajaxWait($session, 20000);
        sleep(1);

        $numrowsgrid1 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(1, $numrowsgrid1);

        /* Ricerca 1 */
        $elementsearch2 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch2 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $search2 = '1°';
        //$page->selectFieldOption('inizia con', "cn");
        $var2 = '"cn"';
        $javascript2 = "$('.selectopts option[value=" . $var2 . "]').attr('selected', 'selected').change();;";

        $session->executeScript($javascript2);
        $page->fillField('jqg1', $search2);
        parent::ajaxWait($session, 20000);

        $page->find('css', 'a#fbox_list1_search')->click();
        parent::ajaxWait($session, 20000);

        $numrowsgrid2 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(5, $numrowsgrid2);
        parent::ajaxWait($session, 20000);
        sleep(1);

        /* doppia condizione */
        $elementsearch3 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch3 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $search3 = 100;
        $addrulejs = "$('.ui-add').click();";
        parent::ajaxWait($session, 20000);

        $session->executeScript($addrulejs);
        parent::ajaxWait($session, 20000);

        $var3 = '"intero"';
        $selector3 = '#fbox_list1.searchFilter table.group.ui-widget.ui-widget-content tbody tr td.columns select:first';
        $javascript3 = "$('" . $selector3 . ' option[value=' . $var3 . "]').attr('selected', 'selected').change();";
        parent::ajaxWait($session, 20000);
        $session->executeScript($javascript3);
        parent::ajaxWait($session, 20000);
        $page->fillField('jqg4', $search3);

        $var4 = '"ge"';
        $javascript4 = "$('.selectopts:first option[value=" . $var4 . "]').attr('selected', 'selected').change();;";
        $session->executeScript($javascript4);
        $search5 = '6°';
        $page->fillField('jqg3', $search5);

        $page->find('css', 'a#fbox_list1_search')->click();
        parent::ajaxWait($session, 20000);

        $numrowsgrid3 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(1, $numrowsgrid3);

        //reset filtri
        $elementsearch4 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch4 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $page->find('css', 'a#fbox_list1_reset')->click();
        sleep(1);


        /* Ricerca 4 */
        $elementsearch4 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch4 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        /**/
        $var5 = '"attivo"';
        $selector5 = '#fbox_list1.searchFilter table.group.ui-widget.ui-widget-content tbody tr td.columns select:first';
        $javascript5 = "$('" . $selector5 . ' option[value=' . $var5 . "]').attr('selected', 'selected').change();";
        parent::ajaxWait($session, 20000);
        $session->executeScript($javascript5);
        parent::ajaxWait($session, 20000);
        /**/

        $page->find('css', 'a#fbox_list1_search')->click();
        parent::ajaxWait($session, 20000);

        $numrowsgrid5 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(9, $numrowsgrid5);
        parent::ajaxWait($session, 20000);
        sleep(1);

        /* Ricerca 5 */
        $elementsearch5 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch4 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        /**/
        $var6 = '"true"';
        $selector6 = '.input-elm';
        $javascript6 = "$('" . $selector6 . ' option[value=' . $var6 . "]').attr('selected', 'selected').change();";
        parent::ajaxWait($session, 20000);
        $session->executeScript($javascript6);
        parent::ajaxWait($session, 20000);
        /**/

        $page->find('css', 'a#fbox_list1_search')->click();
        parent::ajaxWait($session, 20000);

        $numrowsgrid5 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(6, $numrowsgrid5);
        parent::ajaxWait($session, 20000);
        sleep(1);

        /* Ricerca 6 */
        $elementsearch6 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch6 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        /**/
        $var6 = '"false"';
        $selector6 = '.input-elm';
        $javascript6 = "$('" . $selector6 . ' option[value=' . $var6 . "]').attr('selected', 'selected').change();";
        parent::ajaxWait($session, 20000);
        $session->executeScript($javascript6);
        parent::ajaxWait($session, 20000);
        /**/

        $page->find('css', 'a#fbox_list1_search')->click();
        parent::ajaxWait($session, 20000);

        $numrowsgrid5 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(3, $numrowsgrid5);
        parent::ajaxWait($session, 20000);
        sleep(1);

        //reset filtri
        $elementsearch6 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch6 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $page->find('css', 'a#fbox_list1_reset')->click();
        sleep(1);
    }

    private function printoperations($session, $page)
    {

        parent::ajaxWait($session, 20000);

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
            sleep(1);
            $this->assertContains('FiFree2', $element->getText());
            $this->assertContains('Ffsecondaria', $element->getText());
            $this->assertContains('Descrizione secondo record', $element->getText());

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
        $client = $this->getClientAutorizzato();
        // @var $em \Doctrine\ORM\EntityManager
        $em = $client->getContainer()->get('doctrine')->getManager();

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

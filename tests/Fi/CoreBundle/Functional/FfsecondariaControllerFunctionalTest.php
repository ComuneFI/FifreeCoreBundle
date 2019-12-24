<?php

use Tests\CoreBundle\FacebookDriver\FacebookDriverTester;

class FfsecondariaControllerFunctionalTest extends FacebookDriverTester
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



        $this->configuratabelleoperation($session, $page);

        $this->validationoperation($session, $page);

        $this->searchoperation($session, $page);

        $this->crudoperation($session, $page);

        $this->printoperations($session, $page);

        $this->logout();

        $session->quit();
    }

    private function crudoperation($session, $page)
    {
        $this->clickElement('buttonadd_list1');

        /* Inserimento */
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'ffsecondaria_';
        } else {
            $fieldprefix = 'fi_corebundle_ffsecondariatype_';
        }

        $this->fillField($fieldprefix . 'descsec', $descrizionetest1);
        $this->selectFieldOption($fieldprefix . 'ffprincipale', 1);
        $this->selectFieldOption($fieldprefix . 'data_day', (int) date('d'));
        $this->selectFieldOption($fieldprefix . 'data_month', (int) date('m'));
        $this->selectFieldOption($fieldprefix . 'data_year', (int) date('Y'));
        $this->fillField($fieldprefix . 'importo', 1000000.12);
        $this->fillField($fieldprefix . 'intero', 1000000);
        $this->fillField($fieldprefix . 'nota', 'Prova la nota');
        $this->checkboxSelect($fieldprefix . 'attivo', 1);
        $this->clickElement('sDataFfsecondariaS');
        $this->ajaxWait();
        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testretid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}(testretid)');
        $this->ajaxWait();

        $this->clickElement('buttonedit_list1');
        $this->ajaxWait();

        /* Modifica */
        $descrizionetest2 = 'Test inserimento descrizione automatico 2';
        $this->fillField($fieldprefix . 'descsec', $descrizionetest2);
        $this->clickElement('sDataFfsecondariaS');
        $this->ajaxWait();

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}(testid)');

        $this->clickElement('buttonedit_list1');


        $selector = "#" . $fieldprefix . 'descsec';
        $iffindelementrightclick = $this->rightClickElement($selector);
        //Metto questo if perchè non è stato implementato per firefox con geckodriver ancora
        if ($iffindelementrightclick) {
            $this->clickElement('jqContextMenu');

            //$this->pressButton('Ok');

            $this->clickElement('ui-dialog-titlebar-close');
            $this->clickElement('fi-default-chiudi');
        }

        $this->searchmodifiche($descrizionetest1);
        /* Cancellazione */
        $jsSetFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSetFirstRow . '}(testid)');
        $this->clickElement('buttondel_list1');
        $this->clickElement('dData');
    }

    private function configuratabelleoperation($session, $page)
    {
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'ffsecondaria_';
        } else {
            $fieldprefix = 'fi_corebundle_ffsecondariatype_';
        }
        /**/
        $this->clickElement('buttonconfig_list1');
        $jsSetFirstRow = '$("#listconfigura").jqGrid("setSelection", rowidcal);';
        $this->evaluateScript('var testcalid = function(){ var rowidcal = $($("#listconfigura").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSetFirstRow . '}(testcalid )');
        $this->ajaxWait();

        $em = $this->doctrine->getManager();

        $qu = $em->createQueryBuilder();
        $qu->select(array('c'))
                ->from('FiCoreBundle:Tabelle', 'c')
                ->where("c.operatori_id = 1")
                ->andWhere("c.nometabella = 'Ffsecondaria'")
                ->andWhere("c.nomecampo = 'attivo'");
        $ff = $qu->getQuery()->getResult();
        $idtab = $ff[0];
        $checkbox = $idtab->getId() . "_mostraindex";
        $this->checkboxSelect($checkbox, 0);

        //$script = 'var ischecked = function(){var ischecked = $("input[name=mostraindex]").prop("checked", false);return ischecked;}(ischecked)';
        $ischecked = $this->checkboxIsChecked($checkbox);
        $this->assertEquals(false, $ischecked);

        //$script = 'function(){$("#listconfigura").trigger("keydown", {which: 50});}()';
        //$this->evaluateScript($script);
        $jsSaveFirstRow = '$("#listconfigura").saveRow(rowidcalsave);';
        $this->evaluateScript('var rowsave = function(){ var rowidcalsave = $($("#listconfigura").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSaveFirstRow . '}(rowsave)');
        $this->ajaxWait();

        $this->clickElement('.ui-icon-circle-close');

        $this->assertTrue(!$this->elementIsVisible("jqgh_list1_attivo"));

        /**/

        $this->clickElement('buttonconfig_list1');
        $jsSetFirstRow = '$("#listconfigura").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var rowid = function(){ var rowid = $($("#listconfigura").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSetFirstRow . '}(rowid)');
        $this->ajaxWait();

        $this->checkboxSelect($checkbox, 1);
        $ischecked = $this->checkboxIsChecked($checkbox);
        $this->assertEquals(true, $ischecked);


        $jsSaveFirstRow = '$("#listconfigura").saveRow(rowidcalsave);';
        $this->evaluateScript('var rowsave = function(){ var rowidcalsave = $($("#listconfigura").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSaveFirstRow . '}(rowsave)');
        $this->ajaxWait();

        $this->clickElement('.ui-icon-circle-close');

        $this->assertTrue($this->elementIsVisible("jqgh_list1_attivo"));
    }

    private function validationoperation($session, $page)
    {
        $this->clickElement('buttonadd_list1');

        /* Inserimento */
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'ffsecondaria_';
        } else {
            $fieldprefix = 'fi_corebundle_ffsecondariatype_';
        }
        $this->fillField($fieldprefix . 'descsec', $descrizionetest1);
        $this->selectFieldOption($fieldprefix . 'ffprincipale', 1);
        $this->selectFieldOption($fieldprefix . 'data_day', (int) date('d'));
        $this->selectFieldOption($fieldprefix . 'data_month', (int) date('m'));
        $this->selectFieldOption($fieldprefix . 'data_year', (int) date('Y'));
        $this->fillField($fieldprefix . 'importo', 1);
        $this->fillField($fieldprefix . 'intero', 1);
        $this->fillField($fieldprefix . 'nota', 'Prova la nota validation');
        $this->checkboxSelect($fieldprefix . 'attivo', 0);


        $this->clickElement('sDataFfsecondariaS');
        $this->assertTrue($this->elementIsVisible('error_list'));

        $this->fillField($fieldprefix . 'importo', 2);
        $this->fillField($fieldprefix . 'intero', 2);

        $this->clickElement('sDataFfsecondariaS');


        /* Cancellazione */
        $jsSetFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var rowid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $jsSetFirstRow . '}(rowid)');

        $this->clickElement('buttondel_list1');

        $this->clickElement('dData');
    }

    private function searchoperation($session, $page)
    {
        /* Ricerca 0 */

        $this->clickElement('search_list1');
        $this->ajaxWait();
        //Test case sensitive
        $search0 = "10° SECONDARIA legato al 2° record principale ed è l'";
        $this->fillField('jqg1', $search0);

        $this->clickElement('fbox_list1_search');
        $this->ajaxWait();


        $numrowsgrid0 = $this->evaluateScript('return $("#list1").jqGrid("getGridParam", "records");');
        $this->assertEquals(1, $numrowsgrid0);
        /* Ricerca 0 */


        $this->clickElement('search_list1');
        $this->ajaxWait();

        $search0 = "L'";
        $var2 = '"cn"';
        $javascript2 = "$('.selectopts option[value=" . $var2 . "]').attr('selected', 'selected').change();;";

        $this->evaluateScript($javascript2);
        $this->fillField('jqg1', $search0);
        $this->ajaxWait();


        $this->clickElement('fbox_list1_search');
        $this->ajaxWait();


        $numrowsgrid0 = $this->evaluateScript('return $("#list1").jqGrid("getGridParam", "records");');
        $this->assertEquals(1, $numrowsgrid0);

        /* Ricerca 0 */

        /* Ricerca 1 */

        $this->clickElement('search_list1');
        $this->ajaxWait();

        $search1 = '9° secondaria';
        $this->fillField('jqg1', $search1);

        $this->clickElement('fbox_list1_search');
        $this->ajaxWait();


        $numrowsgrid1 = $this->evaluateScript('return $("#list1").jqGrid("getGridParam", "records");');
        $this->assertEquals(1, $numrowsgrid1);

        /* Ricerca 1 */

        $this->clickElement('search_list1');
        $this->ajaxWait();

        $search2 = '1°';
        //$this->selectFieldOption('inizia con', "cn");
        $var2 = '"cn"';
        $javascript2 = "$('.selectopts option[value=" . $var2 . "]').attr('selected', 'selected').change();;";

        $this->evaluateScript($javascript2);
        $this->fillField('jqg1', $search2);
        $this->ajaxWait();


        $this->clickElement('fbox_list1_search');
        $this->ajaxWait();


        $numrowsgrid2 = $this->evaluateScript('return $("#list1").jqGrid("getGridParam", "records");');
        $this->assertEquals(5, $numrowsgrid2);
        $this->ajaxWait();


        /* doppia condizione */
        $this->clickElement('search_list1');
        $search3 = 100;
        $addrulejs = "$('.ui-add').click();";
        $this->ajaxWait();

        $this->evaluateScript($addrulejs);
        $this->ajaxWait();

        $var3 = '"intero"';
        $selector3 = '#fbox_list1.searchFilter table.group.ui-widget.ui-widget-content tbody tr td.columns select:first';
        $javascript3 = "$('" . $selector3 . ' option[value=' . $var3 . "]').attr('selected', 'selected').change();";
        $this->ajaxWait();
        $this->evaluateScript($javascript3);
        $this->ajaxWait();
        $this->fillField('jqg4', $search3);

        $var4 = '"ge"';
        $javascript4 = "$('.selectopts:first option[value=" . $var4 . "]').attr('selected', 'selected').change();;";
        $this->evaluateScript($javascript4);
        $search5 = '6°';
        $this->fillField('jqg3', $search5);


        $this->clickElement('fbox_list1_search');
        $this->ajaxWait();

        $numrowsgrid3 = $this->evaluateScript('return $("#list1").jqGrid("getGridParam", "records");');
        $this->assertEquals(1, $numrowsgrid3);

        //reset filtri

        $this->ajaxWait();
        $this->clickElement('search_list1');
        $this->ajaxWait();

        $this->ajaxWait();
        $this->clickElement('fbox_list1_reset');
        $this->ajaxWait();

        /* Ricerca 4 */
        $this->ajaxWait();
        $this->clickElement('search_list1');
        $this->ajaxWait();
        /**/
        $var5 = '"attivo"';
        $selector5 = '#fbox_list1.searchFilter table.group.ui-widget.ui-widget-content tbody tr td.columns select:first';
        $javascript5 = "$('" . $selector5 . ' option[value=' . $var5 . "]').attr('selected', 'selected').change();";
        $this->ajaxWait();
        $this->evaluateScript($javascript5);
        $this->ajaxWait();
        /**/

        //mi tocca rimettere questo sleep perchè schianta anche dopo il refactor
        sleep(2);
        $this->clickElement('fbox_list1_search');
        $this->ajaxWait();

        $numrowsgrid5 = $this->evaluateScript('return $("#list1").jqGrid("getGridParam", "records");');
        $this->assertEquals(9, $numrowsgrid5);
        $this->ajaxWait();


        /* Ricerca 5 */
        $this->clickElement('search_list1');
        $this->ajaxWait();
        /**/
        $var6 = '"true"';
        $selector6 = '.input-elm';
        $javascript6 = "$('" . $selector6 . ' option[value=' . $var6 . "]').attr('selected', 'selected').change();";
        $this->ajaxWait();
        $this->evaluateScript($javascript6);
        $this->ajaxWait();
        /**/
        $this->clickElement('fbox_list1_search');
        $this->ajaxWait();

        $numrowsgrid5 = $this->evaluateScript('return $("#list1").jqGrid("getGridParam", "records");');
        $this->assertEquals(6, $numrowsgrid5);
        $this->ajaxWait();


        /* Ricerca 6 */
        $this->clickElement('search_list1');
        $this->ajaxWait();

        $var6 = '"false"';
        $selector6 = '.input-elm';
        $javascript6 = "$('" . $selector6 . ' option[value=' . $var6 . "]').attr('selected', 'selected').change();";
        $this->ajaxWait();
        $this->evaluateScript($javascript6);
        $this->ajaxWait();
        /**/

        //mi tocca rimettere questo sleep perchè schianta anche dopo il refactor
        sleep(1);
        $this->clickElement('fbox_list1_search');
        $this->ajaxWait();

        $numrowsgrid5 = $this->evaluateScript('return $("#list1").jqGrid("getGridParam", "records");');
        $this->assertEquals(3, $numrowsgrid5);
        $this->ajaxWait();


        //reset filtri
        $this->clickElement('search_list1');
        //mi tocca rimettere questo sleep perchè schianta anche dopo il refactor
        sleep(1);
        $this->clickElement('fbox_list1_reset');
        $this->ajaxWait();
    }

    private function printoperations($session, $page)
    {
        /* Print pdf */
        $this->clickElement('buttonprint_list1');
        sleep(5);
        $windows = $this->facebookDriver->getWindowHandles();
        $lastwindow = end($windows);
        $this->facebookDriver->switchTo()->window($lastwindow);

        $page = $this->getCurrentPageContent();
        $find = strpos($page, 'name="plugin" id="plugin"');
        if ($find !== false) {
            $this->assertStringContainsString("application/pdf", $page);
        } else {
            $this->assertStringContainsString('Ffsecondaria', $this->facebookDriver->getTitle());
            $this->assertStringContainsString('FiFree2', $page);
            $this->assertStringContainsString('Descrizione secondo record', $page);
        }

        $session->executeScript('window.close();');

        $windows2 = $this->facebookDriver->getWindowHandles();
        $lastwindow2 = end($windows2);
        $this->facebookDriver->switchTo()->window($lastwindow2);
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

        $qd = $em->createQueryBuilder();
        $qd->delete()
                ->from('FiCoreBundle:Storicomodifiche', 'c')
                ->where("c.nometabella= 'Ffsecondaria'")
                ->andWhere("c.nomecampo = 'descsec'")
                ->getQuery()
                ->execute();
    }

}

<?php

use Tests\CoreBundle\FacebookDriver\FacebookDriverTester;

class PermessiControllerFunctionalTest extends FacebookDriverTester
{

    public function testPermessi()
    {
        //$url = $_ENV['HTTP_TEST_HOST'] . $_ENV['HTTP_TEST_URL'];
        $url = "/Permessi";
        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();

        $this->crudoperation($session, $page);

        $session->quit();
    }

    public function crudoperation($session, $page)
    {
        $this->clickElement('buttonadd_list1');
        $fieldprefix = 'permessi_';
        /* Inserimento */
        $this->ajaxWait();
        $descrizionetest1 = 'testmodulo';
        $this->fillField($fieldprefix . 'modulo', $descrizionetest1);
        $this->clickElement('sDataPermessiS');
        $this->ajaxWait();

        $em = $this->em;
        $qb2 = $em->createQueryBuilder();
        $qb2->select(array('a'));
        $qb2->from('FiCoreBundle:Permessi', 'a');
        $qb2->where('a.modulo = :descrizione');
        $qb2->setParameter('descrizione', $descrizionetest1);
        $record2 = $qb2->getQuery()->getResult();
        $recorddelete = $record2[0];
        $this->assertEquals($recorddelete->getModulo(), $descrizionetest1);

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testselid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}(testselid)');


        $this->clickElement('buttonedit_list1');

        /* Modifica */
        $descrizionetest2 = 'testmodulo 2';
        $this->fillField($fieldprefix . 'modulo', $descrizionetest2);
        $this->clickElement('sDataPermessiS');
        $this->ajaxWait();
        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testselid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}(testselid)');


        $this->clickElement('buttondel_list1');

        $this->clickElement('dData');
    }

}

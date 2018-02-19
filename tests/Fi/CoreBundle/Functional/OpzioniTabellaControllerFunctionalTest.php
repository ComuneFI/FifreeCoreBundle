<?php

use Tests\CoreBundle\FacebookDriver\FacebookDriverTester;

class OpzioniTabellaControllerFunctionalTest extends FacebookDriverTester
{

    public function testOpzioniTabella()
    {
        //$url = $_ENV['HTTP_TEST_HOST'] . $_ENV['HTTP_TEST_URL'];
        $url = "/OpzioniTabella";
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

        /* Inserimento */
        $descrizionetest1 = 'testtabella';
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'opzioni_tabella_';
        } else {
            $fieldprefix = 'fi_corebundle_opzionitabellatype_';
        }
        $this->fillField($fieldprefix . 'descrizione', $descrizionetest1);
        $this->selectFieldOption($fieldprefix . 'tabelle', 1);

        $this->clickElement('sDataOpzioniTabellaS');


        $em = $this->em;
        $qb2 = $em->createQueryBuilder();
        $qb2->select(array('a'));
        $qb2->from('FiCoreBundle:OpzioniTabella', 'a');
        $qb2->where('a.descrizione = :descrizione');
        $qb2->setParameter('descrizione', $descrizionetest1);
        $record2 = $qb2->getQuery()->getResult();
        $recorddelete = $record2[0];
        $this->assertEquals($recorddelete->getDescrizione(), $descrizionetest1);


        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testselid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}(testselid)');


        $this->clickElement('buttonedit_list1');

        /* Modifica */
        $descrizionetest2 = 'testtabella 2';
        $this->fillField($fieldprefix . 'descrizione', $descrizionetest2);


        $this->clickElement('sDataOpzioniTabellaS');

        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testselid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}(testselid)');


        $this->clickElement('buttondel_list1');

        $this->clickElement('dData');
    }

}

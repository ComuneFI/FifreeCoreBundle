<?php

use Tests\CoreBundle\FacebookDriver\FacebookDriverTester;

class RuoliControllerFunctionalTest extends FacebookDriverTester
{

    public function testRuoli()
    {
        //$url = $_ENV['HTTP_TEST_HOST'] . $_ENV['HTTP_TEST_URL'];
        $url = "/Ruoli";
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
        $this->ajaxWait();
        
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'ruoli_';
        } else {
            $fieldprefix = 'fi_corebundle_ruolitype_';
        }
        /* Inserimento */
        $this->ajaxWait();
        $descrizionetest1 = 'testruolo';
        
        $this->fillField($fieldprefix . 'ruolo', $descrizionetest1);
        $this->fillField($fieldprefix . 'is_user', 1);
        $this->clickElement('sDataRuoliS');
        $this->ajaxWait();

        $em = $this->em;
        $qb2 = $em->createQueryBuilder();
        $qb2->select(array('a'));
        $qb2->from('FiCoreBundle:Ruoli', 'a');
        $qb2->where('a.ruolo = :descrizione');
        $qb2->setParameter('descrizione', $descrizionetest1);
        $record2 = $qb2->getQuery()->getResult();
        $recorddelete = $record2[0];
        $this->assertEquals($recorddelete->getRuolo(), $descrizionetest1);

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testselid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}(testselid)');


        $this->clickElement('buttonedit_list1');

        /* Modifica */
        $descrizionetest2 = 'testruolo 2';
        $this->fillField($fieldprefix . 'ruolo', $descrizionetest2);


        $this->clickElement('sDataRuoliS');

        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testselid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}(testselid)');


        $this->clickElement('buttondel_list1');

        $this->clickElement('dData');
    }

}

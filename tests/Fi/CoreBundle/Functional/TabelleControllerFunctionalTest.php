<?php

use Tests\CoreBundle\Mink\CoreMink;

class TabelleControllerFunctionalTest extends CoreMink
{

    public function testTabelle()
    {
        //$url = $_ENV['HTTP_TEST_HOST'] . $_ENV['HTTP_TEST_URL'];
        $url = "/Tabelle";
        $this->visit($url);
        $this->login('admin', 'admin');
        $session = $this->getSession();
        $page = $this->getCurrentPage();
        
        sleep(1);
        
        $this->crudoperation($session, $page);

        $session->stop();

    }

    public function crudoperation($session, $page)
    {
        $this->clickElement('#buttonadd_list1');
        /* Inserimento */
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'tabelle_';
        } else {
            $fieldprefix = 'fi_corebundle_tabelletype_';
        }
        $descrizionetest1 = 'testtabella';
        $page->fillField($fieldprefix . 'nometabella', $descrizionetest1);
        sleep(1);
        $this->clickElement('a#sDataTabelleS');
        sleep(1);

        $em = $this->em;
        $qb2 = $em->createQueryBuilder();
        $qb2->select(array('a'));
        $qb2->from('FiCoreBundle:Tabelle', 'a');
        $qb2->where('a.nometabella = :descrizione');
        $qb2->setParameter('descrizione', $descrizionetest1);
        $record2 = $qb2->getQuery()->getResult();
        $recorddelete = $record2[0];
        $this->assertEquals($recorddelete->getNometabella(), $descrizionetest1);

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}()');

        sleep(1);
        $this->clickElement('#buttonedit_list1');
        sleep(1);
        /* Modifica */
        $descrizionetest2 = 'testtabella 2';
        $page->fillField($fieldprefix . 'nometabella', $descrizionetest2);

        sleep(1);
        $this->clickElement('a#sDataTabelleS');
        sleep(1);
        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}()');

        sleep(1);
        $this->clickElement('#buttondel_list1');
        sleep(1);
        $this->clickElement('a#dData');
    }

}

<?php

namespace Fi\CoreBundle\Controller;

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
        $elementadd = $page->findAll('css', '.ui-icon-plus');

        foreach ($elementadd as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        /* Inserimento */
        $this->ajaxWait();
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'tabelle_';
        } else {
            $fieldprefix = 'fi_corebundle_tabelletype_';
        }
        $descrizionetest1 = 'testtabella';
        $page->fillField($fieldprefix . 'nometabella', $descrizionetest1);
        $page->find('css', 'a#sDataTabelleS')->click();
        $this->ajaxWait();

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

        $elementmod = $page->findAll('css', '.ui-icon-pencil');

        foreach ($elementmod as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        $this->ajaxWait();
        /* Modifica */
        $descrizionetest2 = 'testtabella 2';
        $page->fillField($fieldprefix . 'nometabella', $descrizionetest2);

        $page->find('css', 'a#sDataTabelleS')->click();
        $this->ajaxWait();
        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}()');

        $elementdel = $page->findAll('css', '.ui-icon-trash');
        $this->ajaxWait();

        foreach ($elementdel as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        $this->ajaxWait();
        $page->find('css', 'a#dData')->click();
        $this->ajaxWait();
    }

}
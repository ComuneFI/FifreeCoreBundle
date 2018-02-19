<?php

use Tests\CoreBundle\FacebookDriver\FacebookDriverTester;

class MenuApplicazioneControllerFunctionalTest extends FacebookDriverTester
{

    public function testMenuApplicazione()
    {
        $url = "/MenuApplicazione";
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
        $this->ajaxWait();
        $descrizionetest1 = 'testmenu';
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'menu_applicazione';
        } else {
            $fieldprefix = 'fi_corebundle_menuapplicazione';
        }
        $this->fillField($fieldprefix . '_nome', $descrizionetest1);
        $this->fillField($fieldprefix . '_percorso', 'http://www.google.it');
        $this->checkboxSelect($fieldprefix . '_autorizzazionerichiesta', 1);
        $this->checkboxSelect($fieldprefix . '_autorizzazionerichiesta', 1);
        $this->checkboxSelect($fieldprefix . '_autorizzazionerichiesta', 0);
        $this->clickElement('sDataMenuApplicazioneS');
        $this->ajaxWait();

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testselid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}(testselid )');

        
        $this->clickElement('buttonedit_list1');
        
        /* Modifica */
        $descrizionetest2 = 'testmenu 2';
        $this->fillField($fieldprefix . '_nome', $descrizionetest2);

        $this->clickElement('sDataMenuApplicazioneS');
        $this->ajaxWait();

        $em = $this->em;
        $qb2 = $em->createQueryBuilder();
        $qb2->select(array('a'));
        $qb2->from('FiCoreBundle:MenuApplicazione', 'a');
        $qb2->where('a.nome = :descrizione');
        $qb2->setParameter('descrizione', $descrizionetest2);
        $record2 = $qb2->getQuery()->getResult();
        $recorddelete = $record2[0];
        $this->assertEquals($recorddelete->getNome(), $descrizionetest2);

        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $this->evaluateScript('var testselid = function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}(testselid)');

        
        $this->clickElement('buttondel_list1');
        
        $this->clickElement('dData');
    }

}

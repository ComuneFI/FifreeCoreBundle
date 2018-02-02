<?php

use Tests\CoreBundle\Mink\CoreMink;

class MenuApplicazioneControllerFunctionalTest extends CoreMink
{

    public function testMenuApplicazione()
    {
        $url = "/MenuApplicazione";
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
        $this->ajaxWait();
        $descrizionetest1 = 'testmenu';
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'menu_applicazione';
        } else {
            $fieldprefix = 'fi_corebundle_menuapplicazione';
        }
        $page->fillField($fieldprefix . '_nome', $descrizionetest1);
        $page->fillField($fieldprefix . '_percorso', 'http://www.google.it');
        $page->fillField($fieldprefix . '_autorizzazionerichiesta', 1);
        $page->find('css', 'a#sDataMenuApplicazioneS')->click();
        $this->ajaxWait();

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}()');

        sleep(1);
        $this->clickElement('#buttonedit_list1');
        sleep(1);
        /* Modifica */
        $descrizionetest2 = 'testmenu 2';
        $page->fillField($fieldprefix . '_nome', $descrizionetest2);

        $page->find('css', 'a#sDataMenuApplicazioneS')->click();
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
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}()');

        sleep(1);
        $this->clickElement('#buttondel_list1');
        sleep(1);
        $this->clickElement('a#dData');
    }

}

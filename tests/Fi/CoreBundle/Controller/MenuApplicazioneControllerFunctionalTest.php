<?php

namespace Fi\CoreBundle\Controller;

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
        parent::ajaxWait($session, 20000);
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
        /* Modifica */
        $descrizionetest2 = 'testmenu 2';
        $page->fillField($fieldprefix . '_nome', $descrizionetest2);

        $page->find('css', 'a#sDataMenuApplicazioneS')->click();
        parent::ajaxWait($session);

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

        $elementdel = $page->findAll('css', '.ui-icon-trash');
        parent::ajaxWait($session, 20000);

        foreach ($elementdel as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $page->find('css', 'a#dData')->click();
        parent::ajaxWait($session, 20000);
    }

}

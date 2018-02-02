<?php

use Tests\CoreBundle\Mink\CoreMink;

class PermessiControllerFunctionalTest extends CoreMink
{

    public function testPermessi()
    {
        //$url = $_ENV['HTTP_TEST_HOST'] . $_ENV['HTTP_TEST_URL'];
        $url = "/Permessi";
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
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'permessi_';
        } else {
            $fieldprefix = 'fi_corebundle_permessitype_';
        }
        /* Inserimento */
        $this->ajaxWait();
        $descrizionetest1 = 'testmodulo';
        $page->fillField($fieldprefix . 'modulo', $descrizionetest1);
        $page->find('css', 'a#sDataPermessiS')->click();
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
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRow . '}()');

        sleep(1);
        $this->clickElement('#buttonedit_list1');
        sleep(1);
        /* Modifica */
        $descrizionetest2 = 'testmodulo 2';
        $page->fillField($fieldprefix . 'modulo', $descrizionetest2);
        $page->find('css', 'a#sDataPermessiS')->click();
        $this->ajaxWait();
        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");' . $selectFirstRowDel . '}()');

        sleep(1);
        $this->clickElement('#buttondel_list1');
        sleep(1);
        $this->clickElement('a#dData');
    }

}

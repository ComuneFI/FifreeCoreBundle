<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTest;
use Behat\Mink\Mink;
use Behat\Mink\Session;

class Z1FfprincipaleControllerTest extends FifreeTest
{
    /**
     * @test
     */
    public function testIndexFfprincipale()
    {
        parent::setUp();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Ffprincipale');
        $em = $this->getEntityManager();
        $this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        sleep(1);
        $crawler = new Crawler($client->getResponse()->getContent());
        sleep(1);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Ffprincipale"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

        $clientnoauth = $this->getClientNonAutorizzato();
        $urlnoauth = '/Ffprincipale/';
        $clientnoauth->request('GET', $urlnoauth);

        $this->assertEquals($this->getClassName(), get_class());
        $this->assertEquals(302, $clientnoauth->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testExcelFfprincipale()
    {
        parent::__construct();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        //$url = $client->getContainer()->get('router')->generate('Ffprincipale');
        $url = $client->getContainer()->get('router')->generate('Tabelle_esportaexceltabella', array('nometabella' => 'Ffprincipale'));

        $client->request('GET', $url);
        sleep(1);
        $this->assertTrue(
            $client->getResponse()->headers->contains('Content-Type', 'text/csv; charset=UTF-8')
        );
    }

    /*
     * @test
     */

    public function testFfprincipale()
    {
        parent::__construct();
        $this->setClassName(get_class());
        $browser = 'firefox';
        //$url = $client->getContainer()->get('router')->generate('Categoria_container');
        $url = 'http://127.0.0.1:8000/app_test.php/';

        // Choose a Mink driver. More about it in later chapters.
        $driver = new \Behat\Mink\Driver\Selenium2Driver($browser);
        $session = new Session($driver);
        // start the session
        $session->start();
        $session->visit($url);
        $page = $session->getPage();
        sleep(1);
        /* Login */
        $page->fillField('username', 'admin');
        $page->fillField('password', 'admin');
        $page->pressButton('_submit');
        //$page = $session->getPage();

        sleep(3);
        $this->crudoperation($session, $page);

        $this->searchoperation($session, $page);

        $this->printoperations($session, $page);

        $session->stop();

        /* $client = $this->getClientAutorizzato();
          // @var $em \Doctrine\ORM\EntityManager
          $em = $client->getContainer()->get('doctrine')->getManager();

          $qu = $em->createQueryBuilder();
          $qu->select(array('c'))
          ->from('FiCoreBundle:Ffprincipale', 'c')
          ->where('c.descrizione = :descrizione')
          ->setParameter('descrizione', $descrizionetest);
          $ff = $qu->getQuery()->getSingleResult();
          $this->assertEquals($ff->getDescrizione(), $descrizionetest);

          $em->remove($ff);
          $em->flush();
          $em->clear();
          $this->assertTrue(is_null($ff->getId())); */
    }

    private function searchoperation($session, $page)
    {
        $elementsearch = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        /* Ricerca 1 */
        parent::ajaxWait($session, 20000);
        $search1 = 'primo';
        sleep(1);
        $page->fillField('jqg1', $search1);
        $page->find('css', 'a#fbox_list1_search')->click();
        parent::ajaxWait($session, 20000);

        $numrowsgrid1 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(0, $numrowsgrid1);

        /* Ricerca 1 */
        $elementsearch2 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch2 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $search2 = 'primo';
        sleep(1);
        //$page->selectFieldOption('inizia con', "cn");
        $var2 = '"cn"';
        $javascript2 = "$('.selectopts option[value=".$var2."]').attr('selected', 'selected').change();;";

        $session->executeScript($javascript2);
        $page->fillField('jqg1', $search2);

        $page->find('css', 'a#fbox_list1_search')->click();
        parent::ajaxWait($session, 20000);

        $numrowsgrid2 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(1, $numrowsgrid2);
    }

    private function crudoperation($session, $page)
    {
        $elementadd = $page->findAll('css', '.ui-icon-plus');

        foreach ($elementadd as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        /* Inserimento */
        parent::ajaxWait($session, 20000);
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        $page->fillField('fi_corebundle_ffprincipaletype_descrizione', $descrizionetest1);
        $page->find('css', 'a#sDataFfprincipaleS')->click();
        parent::ajaxWait($session, 20000);

        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");'.$selectFirstRow.'}()');

        $elementmod = $page->findAll('css', '.ui-icon-pencil');

        foreach ($elementmod as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        /* Modifica */
        $descrizionetest2 = 'Test inserimento descrizione automatico 2';
        $page->fillField('fi_corebundle_ffprincipaletype_descrizione', $descrizionetest2);
        $page->find('css', 'a#sDataFfprincipaleS')->click();
        parent::ajaxWait($session);
        /* Cancellazione */
        $selectFirstRowDel = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");'.$selectFirstRowDel.'}()');

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

    private function printoperations($session, $page)
    {
        /* Print pdf */
        $element = $page->findAll('css', '.ui-icon-print');

        foreach ($element as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        $windowNames = $session->getWindowNames();
        if (count($windowNames) > 1) {
            $session->switchToWindow($windowNames[1]);
            $page = $session->getPage();
            sleep(3);
            $element = $page->find('css', '.textLayer');

            if (empty($element)) {
                throw new \Exception("No html element found for the selector 'textLayer'");
            }
            $this->assertContains('FiFree2', $element->getText());
            $this->assertContains('Ffprincipale', $element->getText());
            $this->assertContains('Descrizione primo record', $element->getText());

            $session->executeScript('window.close()');
            $mainwindow = $windowNames[0];
            $session->switchToWindow($mainwindow);
            $page = $session->getPage();
        }
    }
}

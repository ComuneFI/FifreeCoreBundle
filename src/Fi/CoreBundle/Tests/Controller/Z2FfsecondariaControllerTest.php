<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTest;
use Behat\Mink\Mink;
use Behat\Mink\Session;

class Z2FfsecondariaControllerTest extends FifreeTest
{
    /**
     * @test
     */
    public function testIndexFfsecondaria()
    {
        parent::__construct();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Ffsecondaria');
        $em = $this->getEntityManager();
        $this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Ffsecondaria"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

        $clientnoauth = $this->getClientNonAutorizzato();
        $urlnoauth = '/Ffsecondaria/';
        $clientnoauth->request('GET', $urlnoauth);

        $this->assertEquals($this->getClassName(), get_class());
        $this->assertEquals(302, $clientnoauth->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testExcelFfsecondaria()
    {
        parent::__construct();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Tabelle_esportaexceltabella', array('nometabella' => 'Ffsecondaria'));

        $client->request('GET', $url);
        $this->assertTrue(
            $client->getResponse()->headers->contains('Content-Type', 'text/csv; charset=UTF-8')
        );
    }

    /*
     * @test
     */

    public function testFfsecondaria()
    {
        parent::__construct();
        $this->setClassName(get_class());
        $browser = 'firefox';
        $urlruote = $this->getContainer()->get('router')->generate('Ffsecondaria');
        $url = 'http://127.0.0.1:8000'.$urlruote;

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

        $this->crudoperation($session, $page);

        $this->searchoperation($session, $page);

        $this->printoperations($session, $page);

        $session->stop();
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
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        sleep(1);
        $page->fillField('fi_corebundle_ffsecondariatype_descsec', $descrizionetest1);
        $page->selectFieldOption('fi_corebundle_ffsecondariatype_ffprincipale', 1);
        $page->selectFieldOption('fi_corebundle_ffsecondariatype_data_day', (int) date('d'));
        $page->selectFieldOption('fi_corebundle_ffsecondariatype_data_month', (int) date('m'));
        $page->selectFieldOption('fi_corebundle_ffsecondariatype_data_year', (int) date('Y'));
        $page->fillField('fi_corebundle_ffsecondariatype_importo', 1000000.12);
        $page->fillField('fi_corebundle_ffsecondariatype_intero', 1000000);
        $page->fillField('fi_corebundle_ffsecondariatype_nota', 'Prova la nota');
        $page->fillField('fi_corebundle_ffsecondariatype_attivo', 1);

        $page->find('css', 'a#sDataFfsecondariaS')->click();
        sleep(1);
        $selectFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");'.$selectFirstRow.'}()');
        $elementmod = $page->findAll('css', '.ui-icon-pencil');

        foreach ($elementmod as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        /* Modifica */
        $descrizionetest2 = 'Test inserimento descrizione automatico 2';
        sleep(1);
        $page->fillField('fi_corebundle_ffsecondariatype_descsec', $descrizionetest2);
        $page->find('css', 'a#sDataFfsecondariaS')->click();
        sleep(1);
        /* Cancellazione */
        $jsSetFirstRow = '$("#list1").jqGrid("setSelection", rowid);';
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");'.$jsSetFirstRow.'}()');
        $elementdel = $page->findAll('css', '.ui-icon-trash');

        foreach ($elementdel as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        $page->find('css', 'a#dData')->click();
        sleep(1);
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
        sleep(1);
        $search1 = '9° secondaria';
        sleep(1);
        $page->fillField('jqg1', $search1);
        $page->find('css', 'a#fbox_list1_search')->click();
        sleep(1);

        $numrowsgrid1 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(1, $numrowsgrid1);

        /* Ricerca 1 */
        $elementsearch2 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch2 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        sleep(1);
        $search2 = '1°';
        sleep(1);
        //$page->selectFieldOption('inizia con', "cn");
        $var2 = '"cn"';
        $javascript2 = "$('.selectopts option[value=".$var2."]').attr('selected', 'selected').change();;";

        $session->executeScript($javascript2);
        $page->fillField('jqg1', $search2);

        $page->find('css', 'a#fbox_list1_search')->click();
        sleep(1);

        $numrowsgrid2 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(4, $numrowsgrid2);

        /* doppia condizione */
        $elementsearch3 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch3 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        sleep(1);
        $search3 = 100;
        sleep(1);
        $addrulejs = "$('.ui-add').click();";

        $session->executeScript($addrulejs);
        sleep(1);

        $var3 = '"intero"';
        $selector3 = '#fbox_list1.searchFilter table.group.ui-widget.ui-widget-content tbody tr td.columns select:first';
        $javascript3 = "$('".$selector3.' option[value='.$var3."]').attr('selected', 'selected').change();";
        sleep(1);
        $session->executeScript($javascript3);
        $page->fillField('jqg4', $search3);

        $var4 = '"ge"';
        $javascript4 = "$('.selectopts:first option[value=".$var4."]').attr('selected', 'selected').change();;";
        $session->executeScript($javascript4);
        sleep(1);
        $search5 = '6°';
        $page->fillField('jqg3', $search5);

        $page->find('css', 'a#fbox_list1_search')->click();
        sleep(1);

        $numrowsgrid3 = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(1, $numrowsgrid3);

        //reset filtri
        $elementsearch4 = $page->findAll('css', '.ui-icon-search');

        foreach ($elementsearch4 as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        $page->find('css', 'a#fbox_list1_reset')->click();
        sleep(1);
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
        $windowNames = $session->getWindowNames();
        if (count($windowNames) > 1) {
            $session->switchToWindow($windowNames[1]);
            $page = $session->getPage();
            sleep(2);
            $element = $page->find('css', '.textLayer');

            if (empty($element)) {
                throw new \Exception("No html element found for the selector 'textLayer'");
            }
            sleep(1);
            $this->assertContains('FiFree2', $element->getText());
            $this->assertContains('Ffsecondaria', $element->getText());
            $this->assertContains('Descrizione secondo record', $element->getText());

            sleep(1);
            $session->executeScript('window.close()');
            $mainwindow = $windowNames[0];
            sleep(1);
            $session->switchToWindow($mainwindow);
            sleep(1);
            $page = $session->getPage();
        }
    }
}

/* $client = $this->getClientAutorizzato();
          // @var $em \Doctrine\ORM\EntityManager
          $em = $client->getContainer()->get('doctrine')->getManager();

          $qu = $em->createQueryBuilder();
          $qu->select(array('c'))
          ->from('FiCoreBundle:Ffsecondaria', 'c')
          ->where('c.descrizione = :descrizione')
          ->setParameter('descrizione', $descrizionetest);
          $ff = $qu->getQuery()->getSingleResult();
          $this->assertEquals($ff->getDescrizione(), $descrizionetest);

          $em->remove($ff);
          $em->flush();
          $em->clear();
          $this->assertTrue(is_null($ff->getId())); */

        /* Print excel */
        /* $element = $page->findAll('css', '.ui-icon-circle-arrow-s');

          foreach ($element as $e) {
          if ($e->isVisible()) {
          $e->click();
          }
          }
          $windowNames = $session->getWindowNames();
          if (count($windowNames) > 1) {
          for ($x = 1; $x <= count($windowNames) - 1; $x++) {
          $session->switchToWindow($windowNames[$x]);
          }
          $mainwindow = $windowNames[0];
          $session->switchToWindow($mainwindow);
          $page = $session->getPage();
          } */

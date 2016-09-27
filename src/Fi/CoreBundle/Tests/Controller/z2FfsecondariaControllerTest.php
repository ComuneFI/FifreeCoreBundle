<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTest;
use Behat\Mink\Mink;
use Behat\Mink\Session;

class z2FfsecondariaControllerTest extends FifreeTest {

    private $container;

    public function setUp() {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
    }

    /**
     * @test
     */
    public function testIndexFfsecondaria() {
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
    public function testExcelFfsecondaria() {
        parent::__construct();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Tabelle_esportaexceltabella', array("nometabella" => 'Ffsecondaria'));

        $client->request('GET', $url);
        $this->assertTrue(
                $client->getResponse()->headers->contains(
                        'Content-Type', 'text/csv; charset=UTF-8'
                ), 'the "Content-Type" header is "text/csv; charset=UTF-8"' // optional message shown on failure
        );
    }

    /*
     * @test
     */

    public function testFfsecondaria() {
        parent::__construct();
        $this->setClassName(get_class());
        $browser = 'firefox';
        $urlruote = $this->container->get('router')->generate('Ffsecondaria');
        $url = 'http://127.0.0.1:8000' . $urlruote;

        // Choose a Mink driver. More about it in later chapters.
        $driver = new \Behat\Mink\Driver\Selenium2Driver($browser);
        $session = new Session($driver);
        // start the session
        $session->start();
        $session->visit($url);
        $page = $session->getPage();

        /* Login */
        $page->fillField('username', 'admin');
        $page->fillField('password', 'admin');
        $page->pressButton('_submit');
        //$page = $session->getPage();

        $element = $page->findAll('css', '.ui-icon-plus');

        foreach ($element as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        /* Inserimento */
        sleep(1);
        $descrizionetest1 = 'Test inserimento descrizione automatico';
        sleep(1);
        $page->fillField('fi_corebundle_ffsecondariatype_descsec', $descrizionetest1);
        $page->selectFieldOption('fi_corebundle_ffsecondariatype_ffprincipale', 1);
        $page->selectFieldOption('fi_corebundle_ffsecondariatype_data_day', (int) date("d"));
        $page->selectFieldOption('fi_corebundle_ffsecondariatype_data_month', (int) date("m"));
        $page->selectFieldOption('fi_corebundle_ffsecondariatype_data_year', (int) date("Y"));
        $page->fillField('fi_corebundle_ffsecondariatype_importo', 1000000.12);
        $page->fillField('fi_corebundle_ffsecondariatype_intero', 1000000);
        $page->fillField('fi_corebundle_ffsecondariatype_nota', "Prova la nota");
        $page->fillField('fi_corebundle_ffsecondariatype_attivo', 1);

        $page->find('css', 'a#sDataFfsecondariaS')->click();
        sleep(1);

        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");$("#list1").jqGrid("setSelection", rowid);}()');
        $element = $page->findAll('css', '.ui-icon-pencil');

        foreach ($element as $e) {
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
        $session->evaluateScript('function(){ var rowid = $($("#list1").find(">tbody>tr.jqgrow:first")).attr("id");$("#list1").jqGrid("setSelection", rowid);}()');
        $element = $page->findAll('css', '.ui-icon-trash');

        foreach ($element as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        $page->find('css', 'a#dData')->click();
        sleep(1);

        $this->printoperations($session, $page);

        $session->stop();


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
    }

    private function printoperations($session, $page) {

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
            sleep(1);
            $page = $session->getPage();
            $element = $page->find('css', ".textLayer");

            if (empty($element)) {
                throw new \Exception("No html element found for the selector 'textLayer'");
            }
            sleep(1);
            $this->assertContains("FiFree2", $element->getText());
            $this->assertContains("Ffsecondaria", $element->getText());
            $this->assertContains("Descrizione secondo record", $element->getText());

            sleep(1);
            $session->executeScript('window.close()');
            $mainwindow = $windowNames[0];
            sleep(1);
            $session->switchToWindow($mainwindow);
            sleep(1);
            $page = $session->getPage();
        }
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
    }

}

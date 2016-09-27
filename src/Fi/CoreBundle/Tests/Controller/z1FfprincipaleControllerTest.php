<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTest;
use Behat\Mink\Mink;
use Behat\Mink\Session;

class z1FfprincipaleControllerTest extends FifreeTest {

    /**
     * @test
     */
    public function testIndexFfprincipale() {
        parent::__construct();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Ffprincipale');
        $em = $this->getEntityManager();
        $this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
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
    public function testExcelFfprincipale() {
        parent::__construct();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        //$url = $client->getContainer()->get('router')->generate('Ffprincipale');
        $url = $client->getContainer()->get('router')->generate('Tabelle_esportaexceltabella', array("nometabella" => 'Ffprincipale'));

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

    public function testFfprincipale() {
        parent::__construct();
        $this->setClassName(get_class());
        $browser = 'firefox';
        //$url = $client->getContainer()->get('router')->generate('Categoria_container');
        $url = 'http://127.0.0.1:8000/';

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
        $page->fillField('fi_corebundle_ffprincipaletype_descrizione', $descrizionetest1);
        $page->find('css', 'a#sDataFfprincipaleS')->click();
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
        $page->fillField('fi_corebundle_ffprincipaletype_descrizione', $descrizionetest2);
        $page->find('css', 'a#sDataFfprincipaleS')->click();
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
            $this->assertContains("FiFree2",$element->getText());
            $this->assertContains("Ffprincipale",$element->getText());
            $this->assertContains("Descrizione primo record",$element->getText());

            sleep(1);
            $session->executeScript('window.close()');
            $mainwindow = $windowNames[0];
            sleep(1);
            $session->switchToWindow($mainwindow);
            sleep(1);
            $page = $session->getPage();
        }
        /* Print excel */
        $element = $page->findAll('css', '.ui-icon-circle-arrow-s');

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
        }
    }

}
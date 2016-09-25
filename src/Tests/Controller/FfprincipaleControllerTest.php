<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTest;
use Behat\Mink\Mink;
use Behat\Mink\Session;

class FfprincipaleControllerTest extends FifreeTest {

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

    /*
     * @test
     */

    public function testAddFfprincipale() {
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

        sleep(1);
        $descrizionetest = 'Test inserimento descrizione automatico';
        $page->fillField('fi_corebundle_ffprincipale_descrizione', $descrizionetest);
        $page->find('css', 'a#sDataFfprincipaleS')->click();
        sleep(1);
        $session->stop();

        $client = $this->getClientAutorizzato();
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
        $this->assertTrue(is_null($ff->getId()));

        //$session->wait(5000, "$('.ui-icon-plus').visible");
        //$findName = $page->find("css", ".ui-icon-plus");
        //$findName->click();
        //$session->evaluateScript("$('#grid1').jqGrid('getGridParam', 'selrow')");
        //$findName = $page->find("css", "#addjqgridrow");
        //$findName->click();
        //$session->evaluateScript("jQuery('#addjqgridrow');");
        //$js = 'jQuery("#list1").jqGrid("addRow","new");';
        //$session->evaluateScript($js);
        //$page->pressButton("jQuery('#list1').jqGrid('addjqgridrow');");
        //$session->stop();
    }

}

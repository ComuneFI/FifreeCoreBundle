<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestUtil;
use Behat\Mink\Mink;
use Behat\Mink\Session;

class PermessiControllerTest extends FifreeTestUtil
{

    /**
     * @test
     */
    public function testIndexPermessi()
    {
        parent::setUp();
        $this->setClassName(get_class());
        $client = $this->getClientAutorizzato();
        $url = $client->getContainer()->get('router')->generate('Permessi');
        $em = $this->getEntityManager();
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        sleep(1);
        $crawler = new Crawler($client->getResponse()->getContent());
        sleep(1);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Permessi"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

        $clientnoauth = $this->getClientNonAutorizzato();
        $urlnoauth = '/Permessi/';
        $clientnoauth->request('GET', $urlnoauth);

        $this->assertEquals($this->getClassName(), get_class());
        $this->assertEquals(302, $clientnoauth->getResponse()->getStatusCode());
    }

    public function testPermessi()
    {
        parent::__construct();
        $this->setClassName(get_class());
        $browser = 'firefox';
        $urlRouting = $this->getContainer()->get('router')->generate('Permessi');
        $url = $_ENV['HTTP_TEST_URL'] . $urlRouting;

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

        sleep(1);
        $this->crudoperation($session, $page);

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

    public function crudoperation($session, $page)
    {
        $elementadd = $page->findAll('css', '.ui-icon-plus');

        foreach ($elementadd as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'permessi_';
        } else {
            $fieldprefix = 'fi_corebundle_permessitype_';
        }
        /* Inserimento */
        parent::ajaxWait($session, 20000);
        $descrizionetest1 = 'testmodulo';
        $page->fillField($fieldprefix . 'modulo', $descrizionetest1);
        $page->find('css', 'a#sDataPermessiS')->click();
        parent::ajaxWait($session, 20000);
        
        $em = $this->getEntityManager();
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

        $elementmod = $page->findAll('css', '.ui-icon-pencil');

        foreach ($elementmod as $e) {
            if ($e->isVisible()) {
                $e->click();
            }
        }
        parent::ajaxWait($session, 20000);
        /* Modifica */
        $descrizionetest2 = 'testmodulo 2';
        $page->fillField($fieldprefix . 'modulo', $descrizionetest2);
        $page->find('css', 'a#sDataPermessiS')->click();
        parent::ajaxWait($session);
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

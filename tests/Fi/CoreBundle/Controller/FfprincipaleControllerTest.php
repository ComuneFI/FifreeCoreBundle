<?php

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;
use Symfony\Component\Routing\Route;

class FfprincipaleControllerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     * @covers Fi\CoreBundle\Controller\FiController::<public>
     * @covers Fi\CoreBundle\Entity\OpzioniTabellaRepository::editTestataFormTabelle
     */
    public function testIndexFfprincipale()
    {
        /* @var $client \Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->getClient();
        $url = $this->getRoute('Ffprincipale');
        $em = $this->getEntityManager();
        //$this->assertStringContainsString('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);

        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Ffprincipale"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

        //insert
        $crawler = $client->request('GET', '/Ffprincipale/new');
        $this->assertTrue($crawler->filter('html:contains("formdatiFfprincipale")')->count() > 0);
        $descrizione = "descrizione";
        $fieldprefix = 'ffprincipale';
        $valore = "provacrawler";
        $campodescrizione = $fieldprefix . "[" . $descrizione . "]";
        $form = $crawler->filter('form[id=formdatiFfprincipale]')->form(array("$campodescrizione" => $valore));

        // submit that form
        $crawler = $client->submit($form);

        sleep(1);

        //update
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select(array('a'));
        $qb->from('FiCoreBundle:Ffprincipale', 'a');
        $qb->where('a.descrizione = :descrizione');
        $qb->setParameter('descrizione', $valore);
        $record = $qb->getQuery()->getResult();
        $recordadded = $record[0];

        $this->assertEquals($recordadded->getDescrizione(), $valore);

        $crawler = $client->request('GET', '/Ffprincipale/' . $recordadded->getId() . '/edit');
        $this->assertTrue($crawler->filter('html:contains("formdatiFfprincipale")')->count() > 0);

        $valorenew = "provacrawler2";
        $campodescrizione = $fieldprefix . "[" . $descrizione . "]";
        $form = $crawler->filter('form[id=formdatiFfprincipale]')->form(array("$campodescrizione" => $valorenew));

        // submit that form
        $crawler = $client->submit($form);


        $em->clear();
        $em = $this->getEntityManager();
        $qb2 = $em->createQueryBuilder();
        $qb2->select(array('a'));
        $qb2->from('FiCoreBundle:Ffprincipale', 'a');
        $qb2->where('a.descrizione = :descrizione');
        $qb2->setParameter('descrizione', $valorenew);
        $record2 = $qb2->getQuery()->getResult();
        $recorddelete = $record2[0];
        $this->assertEquals($recorddelete->getDescrizione(), $valorenew);

        //aggiorna
        $crawler = $client->request('POST', '/Ffprincipale/aggiorna', array('id' => $recorddelete->getId()));
        $this->assertTrue($client->getResponse()->getStatusCode() == '404');
        $this->assertGreaterThan(
                0, $crawler->filter('html:contains("Implementare a seconda")')->count()
        );

        //delete
        $crawler = $client->request('GET', '/Ffprincipale/' . $recorddelete->getId() . '/edit');
        $this->assertTrue($crawler->filter('html:contains("formdatiFfprincipale")')->count() > 0);
        $btn = $crawler->selectLink("Elimina")->link();
        $client->click($btn);

        //Non si riesce a premere Cancella si vede perchè viene creato a runtime il pulsante
        //quindi lancio la delete a mano

        $crawler = $client->request('GET', '/Ffprincipale/' . $recorddelete->getId() . '/delete');
    }

    /**
     * @test
     * @covers Fi\CoreBundle\Controller\FiCoreController::<public>
     * @covers Fi\CoreBundle\Controller\FiController::<public>
     * @covers Fi\CoreBundle\DependencyInjection\EsportaTabellaXls::esportaexcel
     * @covers Fi\CoreBundle\DependencyInjection\EsportaTabellaXls::printHeaderXls
     * @covers Fi\CoreBundle\DependencyInjection\EsportaTabellaXls::printBodyXls
     * 
     */
    public function testExcelFfprincipale()
    {
        $client = $this->getClient();
        $url = $this->getRoute('Tabelle_esportaexceltabella', array('nometabella' => 'Ffprincipale'));


        $client->request('GET', $url);

        $this->assertTrue(
                $client->getResponse()->headers->contains('Content-Type', 'text/csv; charset=UTF-8')
        );
    }

    /**
     * @test
     * 
     * @covers Fi\CoreBundle\Controller\FiCoreController::<public>
     * @covers Fi\CoreBundle\Controller\FiController::<public>
     * @covers Fi\CoreBundle\DependencyInjection\EsportaTabellaPdf::stampa
     * @covers Fi\CoreBundle\DependencyInjection\EsportaTabellaPdf::stampaTestata
     * @covers Fi\CoreBundle\DependencyInjection\EsportaTabellaPdf::stampaDettaglio
     * 
     */
    public function testStampaFfprincipale()
    {
        $client = $this->getClient();
        //$url = $client->getContainer()->get('router')->generate('Ffprincipale');
        $url = $this->getRoute('Tabelle_stampatabella', array('nometabella' => 'Ffprincipale'));
        ob_start();
        $client->request('GET', $url);
        $pdfcontents = ob_get_clean();

        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200
        );
    }

}

<?php

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class OpzioniTabellaControllerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     */
    public function testIndexOpzioniTabella()
    {
        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('OpzioniTabella');
        $em = $this->getEntityManager();
        //$this->assertStringContainsString('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $crawler = new Crawler($client->getResponse()->getContent());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="OpzioniTabella"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');


//insert
        $crawler = $client->request('GET', '/OpzioniTabella/new');
        $this->assertTrue($crawler->filter('html:contains("formdatiOpzioniTabella")')->count() > 0);
        $descrizione = "descrizione";
        /* Inserimento */
        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
            $fieldprefix = 'opzioni_tabella';
        } else {
            $fieldprefix = 'fi_corebundle_opzioni_tabellatype';
        }
        $valore = "provacrawler";
        $campodescrizione = $fieldprefix . "[" . $descrizione . "]";
        $campotabella= $fieldprefix . "[tabelle]";
        $form = $crawler->filter('form[id=formdatiOpzioniTabella]')->form(array("$campodescrizione" => $valore,"$campotabella" => 1));

        // submit that form
        $crawler = $client->submit($form);

        sleep(1);

        //update
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select(array('a'));
        $qb->from('FiCoreBundle:OpzioniTabella', 'a');
        $qb->where('a.descrizione = :descrizione');
        $qb->setParameter('descrizione', $valore);
        $record = $qb->getQuery()->getResult();
        $recordadded = $record[0];

        $this->assertEquals($recordadded->getDescrizione(), $valore);

        $crawler = $client->request('GET', '/OpzioniTabella/' . $recordadded->getId() . '/edit');
        $this->assertTrue($crawler->filter('html:contains("formdatiOpzioniTabella")')->count() > 0);

        $valorenew = "provacrawler2";
        $campodescrizione = $fieldprefix . "[" . $descrizione . "]";
        $form = $crawler->filter('form[id=formdatiOpzioniTabella]')->form(array("$campodescrizione" => $valorenew));

        // submit that form
        $crawler = $client->submit($form);


        $em->clear();
        $em = $this->getEntityManager();
        $qb2 = $em->createQueryBuilder();
        $qb2->select(array('a'));
        $qb2->from('FiCoreBundle:OpzioniTabella', 'a');
        $qb2->where('a.descrizione = :descrizione');
        $qb2->setParameter('descrizione', $valorenew);
        $record2 = $qb2->getQuery()->getResult();
        $recorddelete = $record2[0];
        $this->assertEquals($recorddelete->getDescrizione(), $valorenew);

        //delete
        $crawler = $client->request('GET', '/OpzioniTabella/' . $recorddelete->getId() . '/edit');
        $this->assertTrue($crawler->filter('html:contains("formdatiOpzioniTabella")')->count() > 0);
        $btn = $crawler->selectLink("Elimina")->link();
        $client->click($btn);

        //Non si riesce a premere Cancella si vede perchè viene creato a runtime il pulsante
        //quindi lancio la delete a mano

        $crawler = $client->request('GET', '/OpzioniTabella/' . $recorddelete->getId() . '/delete');
    }

}

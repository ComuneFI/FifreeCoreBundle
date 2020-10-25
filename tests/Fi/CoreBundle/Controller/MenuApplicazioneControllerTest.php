<?php

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class MenuApplicazioneControllerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     */
    public function testIndexMenuApplicazione()
    {
        $client = self::getClient();
        $url = $client->getContainer()->get('router')->generate('MenuApplicazione');
        $em = $this->getEntityManager();
        //$this->assertStringContainsString('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        sleep(1);
        $crawler = new Crawler($client->getResponse()->getContent());
        sleep(1);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="MenuApplicazione"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');


//insert
        $crawler = $client->request('GET', '/MenuApplicazione/new');
        $this->assertTrue($crawler->filter('html:contains("formdatiMenuApplicazione")')->count() > 0);
        $descrizione = "nome";
        $fieldprefix = 'menu_applicazione';
        $valore = "provacrawler";
        $campodescrizione = $fieldprefix . "[" . $descrizione . "]";
        $form = $crawler->filter('form[id=formdatiMenuApplicazione]')->form(array("$campodescrizione" => $valore));

        // submit that form
        $crawler = $client->submit($form);

        sleep(1);

        //update
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select(array('a'));
        $qb->from('FiCoreBundle:MenuApplicazione', 'a');
        $qb->where('a.nome = :descrizione');
        $qb->setParameter('descrizione', $valore);
        $record = $qb->getQuery()->getResult();
        $recordadded = $record[0];

        $this->assertEquals($recordadded->getNome(), $valore);

        $crawler = $client->request('GET', '/MenuApplicazione/' . $recordadded->getId() . '/edit');
        $this->assertTrue($crawler->filter('html:contains("formdatiMenuApplicazione")')->count() > 0);

        $valorenew = "provacrawler2";
        $campodescrizione = $fieldprefix . "[" . $descrizione . "]";
        $form = $crawler->filter('form[id=formdatiMenuApplicazione]')->form(array("$campodescrizione" => $valorenew));

        // submit that form
        $crawler = $client->submit($form);


        $em->clear();
        $em = $this->getEntityManager();
        $qb2 = $em->createQueryBuilder();
        $qb2->select(array('a'));
        $qb2->from('FiCoreBundle:MenuApplicazione', 'a');
        $qb2->where('a.nome = :descrizione');
        $qb2->setParameter('descrizione', $valorenew);
        $record2 = $qb2->getQuery()->getResult();
        $recorddelete = $record2[0];
        $this->assertEquals($recorddelete->getNome(), $valorenew);

        //delete
        $crawler = $client->request('GET', '/MenuApplicazione/' . $recorddelete->getId() . '/edit');
        $this->assertTrue($crawler->filter('html:contains("formdatiMenuApplicazione")')->count() > 0);
        $btn = $crawler->selectLink("Elimina")->link();
        $client->click($btn);

        //Non si riesce a premere Cancella si vede perchè viene creato a runtime il pulsante
        //quindi lancio la delete a mano

        $crawler = $client->request('GET', '/MenuApplicazione/' . $recorddelete->getId() . '/delete');
    }

}

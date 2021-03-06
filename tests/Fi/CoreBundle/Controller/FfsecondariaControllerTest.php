<?php

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;
use Fi\CoreBundle\Controller\FfSecondariaController;

class FfsecondariaControllerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     */
    public function testIndexFfsecondaria()
    {
        $em = $this->getEntityManager();

        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('Ffsecondaria');
        //$this->assertStringContainsString('DoctrineORMEntityManager', get_class($em));

        $crawler = $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $body = $crawler->filter('div[id="Ffsecondaria"]');
        $attributes = $body->extract(array('_text', 'class'));
        $this->assertEquals($attributes[0][1], 'tabella');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     * @covers Fi\CoreBundle\Controller\FfSecondariaController::<public>
     * @covers Fi\CoreBundle\Entity\StoricomodificheRepository::<public>
     */
    public function testNewFfsecondaria()
    {
        $em = $this->getEntityManager();

        $client = $this->getClient();

        //insert
        $urlnew = $client->getContainer()->get('router')->generate('Ffsecondaria_new');
        $crawler = $client->request('GET', $urlnew);
        $this->assertTrue($crawler->filter('html:contains("formdatiFfsecondaria")')->count() > 0);
        $descrizione = "descsec";
        $fieldprefix = 'ffsecondaria';
        $valore = "provacrawler";
        $campodescrizione = $fieldprefix . "[" . $descrizione . "]";

        $valoreffprincipale = "1";
        $campoffprincipale = $fieldprefix . "[ffprincipale]";
        $valoredatad = (int) date("d");
        $valoredatam = (int) date("m");
        $valoredatay = (int) date("Y");
        $campodatad = $fieldprefix . "[data][day]";
        $campodatam = $fieldprefix . "[data][month]";
        $campodatay = $fieldprefix . "[data][year]";

        $campoattivo = $fieldprefix . "[attivo]";
        $valoreattivo = 1;

        $campointero = $fieldprefix . "[intero]";
        $valoreintero = 1000;

        $campoimporto = $fieldprefix . "[importo]";
        $valoreimporto = 10000;

        $camponota = $fieldprefix . "[nota]";
        $valorenota = "Notaaaa";

        // submit that form
        $form2 = $crawler->filter('form[id=formdatiFfsecondaria]')->form(array
            ($campodescrizione => $valore,
            $campoffprincipale => $valoreffprincipale,
            $campodatad => $valoredatad,
            $campodatam => $valoredatam,
            $campodatay => $valoredatay,
            $campoattivo => $valoreattivo,
            $campointero => $valoreintero,
            $campoimporto => $valoreimporto,
            $camponota => $valorenota,
        ));

        $crawler = $client->submit($form2);
        //echo $crawler->html();exit;
        //update
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('a'));
        $qb->from('FiCoreBundle:Ffsecondaria', 'a');
        $qb->where('a.descsec = :descrizione');
        $qb->setParameter('descrizione', $valore);
        $record = $qb->getQuery()->getResult();
        $recordadded = $record[0];

        $this->assertEquals($recordadded->getDescsec(), $valore);

        $crawler = $client->request('GET', '/Ffsecondaria/' . $recordadded->getId() . '/edit');
        $this->assertTrue($crawler->filter('html:contains("formdatiFfsecondaria")')->count() > 0);

        $valorenew = "provacrawler2";
        $campodescrizione = $fieldprefix . "[" . $descrizione . "]";
        $form = $crawler->filter('form[id=formdatiFfsecondaria]')->form(array("$campodescrizione" => $valorenew));

        // submit that form
        $crawler = $client->submit($form);
        $em->clear();

        //echo $crawler->html();
        $systementities = $client->getContainer()->get('ficorebundle.entity.system');
        //$systementities->dumpSystemEntities();

        $qs = $em->createQueryBuilder();
        $qs->select(array('c'))
                ->from('FiCoreBundle:Storicomodifiche', 'c')
                ->where("c.nometabella= 'Ffsecondaria'")
                ->andWhere("c.nomecampo = 'descsec'");
        $ffs = $qs->getQuery()->getResult();

        //dump($ffs);
        $this->assertEquals(count($ffs), 1);

        $qb2 = $em->createQueryBuilder();
        $qb2->select(array('a'));
        $qb2->from('FiCoreBundle:Ffsecondaria', 'a');
        $qb2->where('a.descsec = :descrizione');
        $qb2->setParameter('descrizione', $valorenew);
        $record2 = $qb2->getQuery()->getResult();
        //dump($record2);
        $recorddelete = $record2[0];
        $this->assertEquals($recorddelete->getDescsec(), $valorenew);

        //delete
        $crawler = $client->request('GET', '/Ffsecondaria/' . $recorddelete->getId() . '/edit');
        $this->assertTrue($crawler->filter('html:contains("formdatiFfsecondaria")')->count() > 0);
        $btn = $crawler->selectLink("Elimina")->link();
        $client->click($btn);

        //Non si riesce a premere Cancella si vede perchè viene creato a runtime il pulsante
        //quindi lancio la delete a mano

        $crawler = $client->request('GET', '/Ffsecondaria/' . $recorddelete->getId() . '/delete');

        $qu = $em->createQueryBuilder();
        $qu->delete('FiCoreBundle:Storicomodifiche', "s")
                ->where("s.nometabella= 'Ffsecondaria'")
                ->andWhere("s.nomecampo = 'descsec'");
        $fft = $qu->getQuery()->getResult();

        $qu = $em->createQueryBuilder();
        $qu->select(array('c'))
                ->from('FiCoreBundle:Storicomodifiche', 'c')
                ->where("c.nometabella= 'Ffsecondaria'")
                ->andWhere("c.nomecampo = 'descsec'");
        $fftt = $qu->getQuery()->getResult();
        $this->assertEquals(count($fftt), 0);
    }

    /**
     * @test
     * @covers Fi\CoreBundle\Controller\FiCoreController::<public>
     * @covers Fi\CoreBundle\Controller\FiController::<public>
     * @covers Fi\CoreBundle\DependencyInjection\EsportaTabellaXls::printHeaderXls
     * @covers Fi\CoreBundle\DependencyInjection\EsportaTabellaXls::printBodyXls
     * 
     */
    public function testExcelFfsecondaria()
    {
        $client = $this->getClient();
        //$url = $client->getContainer()->get('router')->generate('Ffprincipale');
        $url = $client->getContainer()->get('router')->generate('Tabelle_esportaexceltabella', array('nometabella' => 'Ffsecondaria'));

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
        $url = $client->getContainer()->get('router')->generate('Tabelle_stampatabella', array('nometabella' => 'Ffsecondaria'));
        ob_start();
        $client->request('GET', $url);
        $pdfcontents = ob_get_clean();

        $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200
        );
    }

}

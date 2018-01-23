<?php

namespace Fi\CoreBundle\Tests\Controller;

use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;
use Fi\CoreBundle\Controller\Griglia;

class FfprincipaleUserControllerTest extends FifreeTestAuthorizedClient
{

    public function testIndexFfprincipaleSenzaPrivilegi()
    {

        $client = $this->getClient();

        $url = $client->getContainer()->get('router')->generate('Ffprincipale');

        $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testGrigliaUserMain()
    {
        $namespace = 'Fi';
        $bundle = 'Core';
        $controller = 'Ffsecondaria';
        $client = $this->getClient();
        $container = $this->getClient()->getContainer();

        /* TESTATA */
        $nomebundle = $namespace . $bundle . 'Bundle';
        /* @var $em \Doctrine\ORM\EntityManager */
        /* $em = $this->container->get('doctrine')->getManager(); */
        $descsec = array(array('nomecampo' => 'descsec', 'lunghezza' => '400', 'descrizione' => 'Descrizione tabella secondaria', 'tipo' => 'text'));
        $ffprincipaleId = array(
            array('nomecampo' => 'ffprincipale.descrizione',
                'lunghezza' => '400',
                'descrizione' => 'Descrizione record principale',
                'tipo' => 'text',),
        );
        $dettaglij = array(
            'descsec' => $descsec,
            'ffprincipale_id' => $ffprincipaleId,
        );
        $escludi = array('nota');
        $campiextra = array(
            array('nomecampo' => 'lunghezzanota', 'lunghezza' => '400', 'descrizione' => 'Lunghezza Nota', 'tipo' => 'integer'),
            array('nomecampo' => 'attivoToString', 'lunghezza' => '200', 'descrizione' => 'Attivo string', 'tipo' => 'text'),
        );

        $paricevuti = array(
            'nomebundle' => $nomebundle,
            'nometabella' => $controller,
            'dettaglij' => $dettaglij,
            'campiextra' => $campiextra,
            'escludere' => $escludi,
            'container' => $container
        );

        $testatagriglia = Griglia::testataPerGriglia($paricevuti);
        $modellocolonne = $testatagriglia['modellocolonne'];
        $this->assertEquals(9, count($modellocolonne));

        $this->assertEquals('id', $modellocolonne[0]['name']);
        $this->assertEquals('id', $modellocolonne[0]['id']);
        $this->assertEquals(110, $modellocolonne[0]['width']);
        $this->assertEquals('integer', $modellocolonne[0]['tipocampo']);

        $this->assertEquals('descsec', $modellocolonne[1]['name']);
        $this->assertEquals('descsec', $modellocolonne[1]['id']);
        $this->assertEquals(400, $modellocolonne[1]['width']);
        $this->assertEquals('text', $modellocolonne[1]['tipocampo']);

        $this->assertEquals('ffprincipale.descrizione', $modellocolonne[2]['name']);
        $this->assertEquals('ffprincipale.descrizione', $modellocolonne[2]['id']);
        $this->assertEquals(400, $modellocolonne[2]['width']);
        $this->assertEquals('text', $modellocolonne[2]['tipocampo']);

        $this->assertEquals('data', $modellocolonne[3]['name']);
        $this->assertEquals('data', $modellocolonne[3]['id']);
        $this->assertEquals(110, $modellocolonne[3]['width']);
        $this->assertEquals('date', $modellocolonne[3]['tipocampo']);

        $this->assertEquals('intero', $modellocolonne[4]['name']);
        $this->assertEquals('intero', $modellocolonne[4]['id']);
        $this->assertEquals(110, $modellocolonne[4]['width']);
        $this->assertEquals('integer', $modellocolonne[4]['tipocampo']);

        $this->assertEquals('importo', $modellocolonne[5]['name']);
        $this->assertEquals('importo', $modellocolonne[5]['id']);
        $this->assertEquals(110, $modellocolonne[5]['width']);
        $this->assertEquals('float', $modellocolonne[5]['tipocampo']);

        $this->assertEquals('attivo', $modellocolonne[6]['name']);
        $this->assertEquals('attivo', $modellocolonne[6]['id']);
        $this->assertEquals(110, $modellocolonne[6]['width']);
        $this->assertEquals('boolean', $modellocolonne[6]['tipocampo']);

        $this->assertEquals('lunghezzanota', $modellocolonne[7]['name']);
        $this->assertEquals('lunghezzanota', $modellocolonne[7]['id']);
        $this->assertEquals(400, $modellocolonne[7]['width']);
        $this->assertEquals('integer', $modellocolonne[7]['tipocampo']);
        $this->assertEquals(false, $modellocolonne[7]['search']);

        $this->assertEquals('attivoToString', $modellocolonne[8]['name']);
        $this->assertEquals('attivoToString', $modellocolonne[8]['id']);
        $this->assertEquals(200, $modellocolonne[8]['width']);
        $this->assertEquals('text', $modellocolonne[8]['tipocampo']);
        $this->assertEquals(false, $modellocolonne[8]['search']);

        $tabellagriglia = $testatagriglia['tabella'];
        $nomicolonnegriglia = $testatagriglia['nomicolonne'];
        $this->assertEquals($controller, $tabellagriglia);
        $this->assertEquals(9, count($nomicolonnegriglia));

        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 1;
        $testatagriglia['showadd'] = 1;
        $testatagriglia['showedit'] = 1;
        $testatagriglia['showdel'] = 1;

        $testatagriglia['parametritesta'] = json_encode($paricevuti);
        $FfsecondariaController = new \Fi\CoreBundle\Controller\FfsecondariaController();
        $FfsecondariaController->setContainer($container);

        $crawler = $client->request('GET', '/funzioni/traduzionefiltro', array('filters' => json_encode(array("groupOp" => "AND", "rules" => array(array("field" => "descsec", "op" => "cn", "data" => "secondaria")), array("field" => "attivo", "op" => "eq", "data" => "null")))));
        $this->assertTrue($crawler->filter('html:contains("Descsec")')->count() > 0);

        $requestarray = array(
            'POST',
            '/Ffsecondaria/griglia',
            array('paricevuti' => $paricevuti), 'filters' => json_encode(array("groupOp" => "AND", "rules" => array(array("field" => "descsec", "op" => "cn", "data" => "secondaria")), array("field" => "attivo", "op" => "eq", "data" => "null"))),
            array(),
            array(),
            '',
        );
        $newrequest = new \Symfony\Component\HttpFoundation\Request($requestarray);
        $FfsecondariaController->setParametriGriglia(array('request' => $newrequest));
    }

}

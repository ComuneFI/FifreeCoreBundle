<?php

use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;
use Fi\CoreBundle\Controller\Griglia;
use Behat\Mink\Session;

class GrigliaControllerTest extends FifreeTestAuthorizedClient
{

    /**
     * @test
     */
    public function testGrigliaFilters()
    {
        $namespace = 'Fi';
        $bundle = 'Core';
        $controller = 'Ffsecondaria';
        $client = $this->getClient();
        $container = $client->getContainer();

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
        $tests = $this->getAllTests();


        foreach ($tests as $test) {
            $paricevuti = array(
                'nomebundle' => $nomebundle,
                'nometabella' => $controller,
                'dettaglij' => $dettaglij,
                'campiextra' => array(),
                'escludere' => array(),
                'container' => $container,
                'precondizioniAvanzate' => $test["precondizioniAvanzate"],
                'precondizioni' => $test["precondizioni"],
                "filterarray" => $test["filterarray"]
            );

            $datigriglia = $this->getDatiGriglia($paricevuti);
            $this->assertEquals($test["resultrows"], $datigriglia['total'], $test["descrizionetest"]);
            $this->assertEquals($test["resultrows"], $datigriglia['records'], $test["descrizionetest"]);
        }
    }

    private function getAllTests()
    {
        $tests = array();
        //**************************
        //**************************
        //  PRECONDIZIONI AVANZATE
        //**************************
        //**************************
        //
        //test
        $tests[] = array(
            "descrizionetest" => "Solo filtri da griglia descsec contiene 1",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array
                    (array("field" => "descsec", "op" => "cn", "data" => "1")
                )
            ),
            "resultrows" => 6
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate descsec is not null",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'descsec',
                    'operatore' => 'is not',
                    'valorecampo' => null)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 9
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate descsec is null",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'descsec',
                    'operatore' => 'is',
                    'valorecampo' => null)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 1
        );

        //test
        //Where in - not in
        $listaffsecondaria[] = "1° secondaria legato al 1° record PRINCIPALE";
        $listaffsecondaria[] = "2° SECONDARIA legato al 1° record principale";
        $listaffsecondaria[] = "10° secondaria legato al 2° record principale ed è l'ultimo record";
        $listaffsecondaria[] = "6° secondaria legato al 2° record principale";

        $precondizioniAvanzate = array();
        $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
            'nomecampo' => 'descsec',
            'operatore' => 'in',
            'valorecampo' => $listaffsecondaria);
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate where in array string",
            "precondizioni" => array(),
            "precondizioniAvanzate" => $precondizioniAvanzate,
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 4
        );

        /* TODO: se null la NOT IN deve estrarlo? */
        $precondizioniAvanzate = array();
        $precondizioniAvanzate[] = array('nometabella' => 'Ffsecondaria',
            'nomecampo' => 'descsec',
            'operatore' => 'not in',
            'valorecampo' => $listaffsecondaria);
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate where not in array string",
            "precondizioni" => array(),
            "precondizioniAvanzate" => $precondizioniAvanzate,
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 5 //6
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Stringa uguale a una",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'descsec',
                    'operatore' => '=',
                    'valorecampo' => "1° secondaria legato al 1° record PRINCIPALE")),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 1
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Stringa diversa da una",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'descsec',
                    'operatore' => '<>',
                    'valorecampo' => "1° secondaria legato al 1° record PRINCIPALE")),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 8
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Numero uguale a uno scelto",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'intero',
                    'operatore' => '=',
                    'valorecampo' => 10)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 2
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Numero maggiore a uno scelto",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'intero',
                    'operatore' => '>',
                    'valorecampo' => 10)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 7
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Numero minore a uno scelto",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'intero',
                    'operatore' => '<',
                    'valorecampo' => 100)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 3
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Numero minore o uguale a uno scelto",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'intero',
                    'operatore' => '<=',
                    'valorecampo' => 1000)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 6
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Numero minore a uno scelto BIS",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'intero',
                    'operatore' => '<',
                    'valorecampo' => 1000)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 5
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Numero maggiore uguale a uno scelto BIS",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'intero',
                    'operatore' => '>=',
                    'valorecampo' => 1000)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 5
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Numero diverso a uno scelto",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'intero',
                    'operatore' => '<>',
                    'valorecampo' => 1000)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 9
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Numero where in array",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'intero',
                    'operatore' => 'in',
                    'valorecampo' => array(1, 10))),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 3
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Numero where not in array",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'intero',
                    'operatore' => 'not in',
                    'valorecampo' => array(1, 10))),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 7
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Boolean true",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'attivo',
                    'operatore' => '=',
                    'valorecampo' => true)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 6
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Boolean false",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'attivo',
                    'operatore' => '=',
                    'valorecampo' => false)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 4
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Boolean tutti",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'attivo',
                    'operatore' => '=',
                    'valorecampo' => "null")),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 10
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Boolean is null",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'attivo',
                    'operatore' => 'is',
                    'valorecampo' => null)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 0
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni avanzate Boolean not is null",
            "precondizioni" => array(),
            "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
                    'nomecampo' => 'attivo',
                    'operatore' => 'is not',
                    'valorecampo' => null)),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 10
        );

        //**************************
        //**************************
        //  PRECONDIZIONI NORMALI
        //**************************
        //**************************
        //
        
        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni normali Stringa uguale stringa",
            "precondizioni" => array('descsec' => '1° secondaria legato al 1° record PRINCIPALE'),
            "precondizioniAvanzate" => array(),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 1
        );

        //test
        $tests[] = array(
            "descrizionetest" => "Precondizioni normali intero uguale ad altro",
            "precondizioni" => array('intero' => 10),
            "precondizioniAvanzate" => array(),
            "filterarray" => array(
                "groupOp" => "AND",
                "rules" => array()),
            "resultrows" => 2
        );

        //**************************
        //**************************
        //  FILTRI GRIGLIA
        //**************************
        //**************************
        //
        
        //test Template
        /*
          $tests[] = array(
          "descrizionetest" => "PROVA",
          "precondizioni" => array('descsec' => '1° secondaria legato al 1° record PRINCIPALE'),
          "precondizioniAvanzate" => array(array('nometabella' => 'Ffsecondaria',
          'nomecampo' => 'descsec',
          'operatore' => 'is not',
          'valorecampo' => null)),
          "filterarray" => array(
          "groupOp" => "AND",
          "rules" => array
          (array("field" => "descsec", "op" => "cn", "data" => "1")
          )
          ),
          "resultrows" => 0
          ); */

        return $tests;
    }

    private function getDatiGriglia($paricevuti)
    {

        $requestarray = array(
            'POST',
            '/Ffsecondaria/griglia',
            array('paricevuti' => $paricevuti),
            'filters' => json_encode($paricevuti["filterarray"]),
            array(),
            array(),
            '',
        );

        $newrequest = new \Symfony\Component\HttpFoundation\Request($requestarray);
        $parametrigriglia = array_merge(array("request" => $newrequest), $paricevuti);
        $datigriglia = json_decode(Griglia::datiPerGriglia($parametrigriglia), true);
        return $datigriglia;
    }

    /**
     * @test
     */
    public function testGrigliaMain()
    {
        $namespace = 'Fi';
        $bundle = 'Core';
        $controller = 'Ffsecondaria';
        $client = $this->getClient();
        $container = $client->getContainer();

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
            array('paricevuti' => $paricevuti),
            'filters' => json_encode(array("groupOp" => "AND", "rules" => array(array("field" => "descsec", "op" => "cn", "data" => "secondaria")), array("field" => "attivo", "op" => "eq", "data" => "null"))),
            array(),
            array(),
            '',
        );
        $newrequest = new \Symfony\Component\HttpFoundation\Request($requestarray);
        $FfsecondariaController->setParametriGriglia(array('request' => $newrequest));
        $testatagriglia['parametrigriglia'] = json_encode($FfsecondariaController::$parametrigriglia);

        $testatatabellagriglia = $testatagriglia['tabella'];
        $testatanomicolonnegriglia = $testatagriglia['nomicolonne'];

        $this->assertEquals($controller, $tabellagriglia);
        $this->assertEquals(9, count($testatanomicolonnegriglia));

        $grigliareturn = $FfsecondariaController->grigliaAction($newrequest);
        $datigriglia = json_decode($grigliareturn->getContent());
        if (is_object($datigriglia)) {
            $datigriglia = get_object_vars($datigriglia);
        }

        $this->assertEquals(9, $datigriglia['total']);
        $this->assertEquals(9, count($datigriglia['rows']));

        $rows = $datigriglia['rows'];
        // @var $em \Doctrine\ORM\EntityManager
        $em = $this->container->get('doctrine')->getManager();
        foreach ($rows as $idx => $row) {
            if (is_object($row)) {
                $row = get_object_vars($row);
            }
            if (strpos($modellocolonne[$idx]['name'], '.') > 0) {
                continue;
            }
            $row = $row['cell'];
            if ($modellocolonne[$idx]['tipocampo'] == 'date') {
                $row[$idx] = \DateTime::createFromFormat('d/m/Y', $row[$idx])->format('Y-m-d');
            }
            if (!isset($modellocolonne[$idx]['search']) || !$modellocolonne[$idx]['search'] === false) {
                if (is_null($row[$idx])) {
                    $qu = $em->createQueryBuilder();
                    $qu->select(array('c'))
                            ->from('FiCoreBundle:Ffsecondaria', 'c')
                            ->where('c.' . $modellocolonne[$idx]['name'] . ' is null');

                    $ffrow = $qu->getQuery()->getResult();
                } else {
                    $qu = $em->createQueryBuilder();
                    $qu->select(array('c'))
                            ->from('FiCoreBundle:Ffsecondaria', 'c')
                            ->where('c.' . $modellocolonne[$idx]['name'] . ' = :value')
                            ->setParameter('value', $row[$idx]);
                    $ffrow = $qu->getQuery()->getResult();
                }
                //dump($ffrow);
                //dump($row[$idx]);
                //dump($modellocolonne[$idx]['name']);
                $ff = $ffrow[0];
                $colmacro = 'get' . ucfirst($modellocolonne[$idx]['name']);
                if (!method_exists($ff, $colmacro)) {
                    $colmacro = 'is' . ucfirst($modellocolonne[$idx]['name']);
                    if (!method_exists($ff, $colmacro)) {
                        throw new \Exception("Colonna " . $modellocolonne[$idx]['name'] . " non trovata");
                    }
                }
                if ($modellocolonne[$idx]['tipocampo'] == 'date') {
                    $datadb = \DateTime::createFromFormat('Y-m-d', $ff->$colmacro()->format('Y-m-d'));
                    $datagriglia = \DateTime::createFromFormat('Y-m-d', $row[$idx]);
                    $this->assertEquals($datadb, $datagriglia);
                } else {
                    $this->assertEquals($ff->$colmacro(), $row[$idx]);
                }
            }
        }
    }

}

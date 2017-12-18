<?php

namespace Fi\CoreBundle\Controller;

use Fi\CoreBundle\DependencyInjection\FifreeTestUtil;
use Behat\Mink\Mink;
use Behat\Mink\Session;

class GrigliaControllerTest extends FifreeTestUtil
{

    /**
     * @test
     */
    public function testGrigliaMain()
    {
        parent::setUp();
        $namespace = 'Fi';
        $bundle = 'Core';
        $controller = 'Ffsecondaria';
        $container = $this->getContainer();

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

        /* @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fifree.fos_user.user_manager');
        /* @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('fifree.fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');
        $username4test = $container->getParameter('user4test');
        $user = $userManager->findUserBy(array('username' => $username4test));
        $loginManager->loginUser($firewallName, $user);

        /* save the login token into the session and put it in a cookie */
        $container->get('session')->set('_security_' . $firewallName, serialize($container->get('security.token_storage')->getToken()));
        $container->get('session')->save();

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
        $FfsecondariaController = new FfsecondariaController();
        $FfsecondariaController->setContainer($container);

        $client = parent::getClientAutorizzato();
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
        $testatagriglia['parametrigriglia'] = json_encode($FfsecondariaController::$parametrigriglia);

        $testatatabellagriglia = $testatagriglia;
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
                $qu = $em->createQueryBuilder();
                $qu->select(array('c'))
                        ->from('FiCoreBundle:Ffsecondaria', 'c')
                        ->where('c.' . $modellocolonne[$idx]['name'] . ' = :value')
                        ->setParameter('value', $row[$idx]);
                $ffrow = $qu->getQuery()->getResult();
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

    /*
     * @test
     */

    public function testGrigliaFfsecondaria()
    {
        parent::setUp();
        $this->setClassName(get_class());
        $browser = 'firefox';
        $urlruote = $this->getContainer()->get('router')->generate('Ffsecondaria');
        $url = 'http://127.0.0.1:8000/app_test.php' . $urlruote;

        // Choose a Mink driver. More about it in later chapters.
        $driver = new \Behat\Mink\Driver\Selenium2Driver($browser);
        $session = new Session($driver);
        // start the session
        $session->start();
        $session->visit($url);
        $page = $session->getPage();

        /* Login */
        $username4test = $this->getContainer()->getParameter('user4test');
        $page->fillField('username', $username4test);
        $page->fillField('password', $username4test);
        $page->pressButton('_submit');
        //$page = $session->getPage();

        sleep(1);

        $numrowsgrid = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");return numrow;}()');
        $this->assertEquals(9, $numrowsgrid);

        $session->stop();
    }
}

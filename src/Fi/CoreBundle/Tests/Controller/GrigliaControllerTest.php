<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Fi\CoreBundle\DependencyInjection\FifreeTest;
use Behat\Mink\Mink;
use Behat\Mink\Session;

class GrigliaControllerTest extends FifreeTest {

    private $container;

    public function setUp() {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
    }

    /**
     * @test
     */
    public function testGriglia() {
        parent::__construct();
        $namespace = "Fi";
        $bundle = "Core";
        $controller = "Ffsecondaria";
        $container = $this->container;

        /* TESTATA */
        $nomebundle = $namespace . $bundle . 'Bundle';
        /* @var $em \Doctrine\ORM\EntityManager */
        /* $em = $this->container->get('doctrine')->getManager(); */

        $dettaglij = array(
            'descsec' => array(array('nomecampo' => 'descsec', 'lunghezza' => '400', 'descrizione' => 'Descrizione tabella secondaria', 'tipo' => 'text')),
            'ffprincipale_id' => array(array('nomecampo' => 'ffprincipale.descrizione', 'lunghezza' => '400', 'descrizione' => 'Descrizione record principale', 'tipo' => 'text')),
                /* ,
                  array("nomecampo" => "ffprincipale.id", "lunghezza" => "40", "descrizione" => "IdP", "tipo" => "integer") */
        );
        $escludi = array();
        $paricevuti = array('nomebundle' => $nomebundle, 'nometabella' => $controller, 'dettaglij' => $dettaglij, 'escludere' => $escludi, 'container' => $container);

        /* @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /* @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');
        $username4test = $container->getParameter('user4test');
        $user = $userManager->findUserBy(array('username' => $username4test));
        $loginManager->loginUser($firewallName, $user);

        /* save the login token into the session and put it in a cookie */
        $container->get('session')->set('_security_' . $firewallName, serialize($container->get('security.token_storage')->getToken()));
        $container->get('session')->save();

        $testatagriglia = Griglia::testataPerGriglia($paricevuti);
        $tabellagriglia = $testatagriglia["tabella"];
        $nomicolonnegriglia = $testatagriglia["nomicolonne"];

        $this->assertEquals($controller, $tabellagriglia);
        $this->assertEquals(8, count($nomicolonnegriglia));


        $testatagriglia['multisearch'] = 1;
        $testatagriglia['showconfig'] = 1;
        $testatagriglia['showadd'] = 1;
        $testatagriglia['showedit'] = 1;
        $testatagriglia['showdel'] = 1;

        $testatagriglia['parametritesta'] = json_encode($paricevuti);
        $FfsecondariaController = new FfsecondariaController();
        $FfsecondariaController->setContainer($container);
        $newrequest = new \Symfony\Component\HttpFoundation\Request();
        $FfsecondariaController->setParametriGriglia(array('request' => $newrequest));
        $testatagriglia['parametrigriglia'] = json_encode($FfsecondariaController::$parametrigriglia);

        $testatatabellagriglia = $testatagriglia;
        $testatatabellagriglia = $testatagriglia["tabella"];
        $testatanomicolonnegriglia = $testatagriglia["nomicolonne"];

        $this->assertEquals($controller, $tabellagriglia);
        $this->assertEquals(8, count($testatanomicolonnegriglia));

        /* $this->setClassName(get_class());
          $client = $this->getClientAutorizzato();

          $url = $client->getContainer()->get('router')->generate('Ffsecondaria');
          $em = $this->getEntityManager();
          $this->assertContains('DoctrineORMEntityManager', get_class($em));

          $client->request('GET', $url);
          $crawler = new Crawler($client->getResponse()->getContent());
          $this->assertTrue($client->getResponse()->isSuccessful());
          $body = $crawler->filter('div[id="Ffsecondaria"]');
          $attributes = $body->extract(array('_text', 'class'));
          $this->assertEquals($attributes[0][1], 'tabella');

          $clientnoauth = $this->getClientNonAutorizzato();
          $urlnoauth = '/Ffsecondaria/';
          $clientnoauth->request('GET', $urlnoauth);

          $this->assertEquals($this->getClassName(), get_class());
          $this->assertEquals(302, $clientnoauth->getResponse()->getStatusCode()); */
    }

    /*
     * @test
     */

    public function testGrigliaFfsecondaria() {
        parent::__construct();
        $this->setClassName(get_class());
        $browser = 'firefox';
        $urlruote = $this->container->get('router')->generate('Ffsecondaria');
        $url = 'http://127.0.0.1:8000' . $urlruote;

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

        sleep(1);

        $numrowsgrid = $session->evaluateScript('function(){ var numrow = $("#list1").jqGrid("getGridParam", "records");console.log(numrow);return numrow;}()');
        $this->assertEquals(6, $numrowsgrid);

        $session->stop();
    }

}

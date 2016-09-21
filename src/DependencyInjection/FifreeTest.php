<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class FifreeTest extends WebTestCase
{
    private $clientNonAutorizzato;
    private $clientAutorizzato;
    private $crawler;
    private $testclassname;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct()
    {
        $this->clientNonAutorizzato = static::createClient();
        $this->clientAutorizzato = $this->createAuthorizedClient(static::createClient());

        $this->em = $this->clientAutorizzato->getContainer()->get('doctrine')->getManager();
    }

    public function setClassName($testclassname)
    {
        $this->testclassname = $testclassname;
    }

    public function getClassName()
    {
        return $this->testclassname;
    }

    public function getEm()
    {
        return $this->em;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    public function getClientNonAutorizzato()
    {
        return $this->clientNonAutorizzato;
    }

    public function getClientAutorizzato()
    {
        return $this->clientAutorizzato;
    }

    public function getControllerNameByClassName()
    {
        $classnamearray = explode('\\', $this->testclassname);
        $classname = $classnamearray[count($classnamearray) - 1];
        $controllerName = preg_replace('/ControllerTest/', '', $classname);

        return $controllerName;
    }

    public function fifreeDbBaseTest($entity)
    {
        if (!$this->clientAutorizzato->getContainer()->getParameter('testdb')) {
            return true;
        }

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getEm();

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select(array('a'));
        $qb->from($entity, 'a');
        //$qb->setFirstResult( $offset )
        $qb->setMaxResults(1);
        $risultatoquery = $qb->getQuery()->getResult();

        self::assertGreaterThan(0, count($risultatoquery));

        //$this->assertRegExp('/Elenco corsi/', $client->getResponse()->getContent());
        //$this->assertGreaterThan(0,$crawler->filter('html:contains("Elenco corsi")')->count());
        // Asserire che il codice di stato della risposta sia 404
        //$this->assertTrue($client->getResponse()->isNotFound());
        // Asserire uno specifico codice di stato 200
        //$this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertGreaterThan(0, $crawler->filter('corsi')->count());
    }

    public function fifreeBaseTest()
    {
        //ini_set('memory_limit', '32M');
        if (!$this->clientAutorizzato->getContainer()->getParameter('testroutes')) {
            return true;
        }

        $controller = $this->getControllerNameByClassName();
        $url = $this->clientAutorizzato->getContainer()->get('router')->generate($controller.'_container', array(), false);
        $this->clientAutorizzato->request('GET', $url);

        //$crawler = new Crawler($client->getResponse()->getContent());
        // Asserire che il codice di stato della risposta sia 2xx
        $this->assertTrue($this->clientAutorizzato->getResponse()->isSuccessful());

        //$this->assertRegExp('/Elenco corsi/', $client->getResponse()->getContent());
        //$this->assertGreaterThan(0,$crawler->filter('html:contains("Elenco corsi")')->count());
        // Asserire che il codice di stato della risposta sia 404
        //$this->assertTrue($client->getResponse()->isNotFound());
        // Asserire uno specifico codice di stato 200
        //$this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertGreaterThan(0, $crawler->filter('corsi')->count());
    }

    public static function createAuthorizedClient($client)
    {
        $container = $client->getContainer();

        $session = $container->get('session');
        /* @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /* @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');

        $username4test = $container->getParameter('user4test');
        $user = $userManager->findUserBy(array('username' => $username4test));
        $loginManager->loginUser($firewallName, $user);

        /* save the login token into the session and put it in a cookie */
        $container->get('session')->set('_security_'.$firewallName, serialize($container->get('security.token_storage')->getToken()));
        $container->get('session')->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        
        return $client;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    /*
      // Create a new client to browse the application
      $client = static::createClient();

      // Create a new entry in the database
      $crawler = $client->request('GET', '/immobile/');
      $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /immobile/");
      $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

      // Fill in the form and submit it
      $form = $crawler->selectButton('Create')->form(array(
      'fi_formazionebundle_immobiletype[field_name]'  => 'Test',
      // ... other fields to fill
      ));

      $client->submit($form);
      $crawler = $client->followRedirect();

      // Check data in the show view
      $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

      // Edit the entity
      $crawler = $client->click($crawler->selectLink('Edit')->link());

      $form = $crawler->selectButton('Edit')->form(array(
      'fi_formazionebundle_immobiletype[field_name]'  => 'Foo',
      // ... other fields to fill
      ));

      $client->submit($form);
      $crawler = $client->followRedirect();

      // Check the element contains an attribute with value equals "Foo"
      $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

      // Delete the entity
      $client->submit($crawler->selectButton('Delete')->form());
      $crawler = $client->followRedirect();

      // Check the entity has been delete on the list
      $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
     */
}

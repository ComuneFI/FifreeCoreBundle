<?php

namespace Fi\CoreBundle\Tests\Command;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;

class PannelloAmministrazioneTest extends CommandTestCase
{

    public static $conn;

    public static function setUpBeforeClass()
    {
        startTests();
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        //self::bootKernel();
        //$this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testPannelloGenerateBundle()
    {
        $client = self::createClient();
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($client->getContainer());

        $output = $this->runCommand($client, "generate:bundle --namespace=Fi/ProvaBundle --dir=src/ --format=yml --env=test --no-interaction");
        echo $output;

        $cmddcc = "doctrine:cache:clear-metadata";
        passthru(sprintf(
                        'php "%s/console" ' . $cmddcc . ' --env=%s', __DIR__ . '/../../app/', $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
        ));

        $cmdcc = "cache:clear";
        passthru(sprintf(
                        'php "%s/console" ' . $cmdcc . ' --env=%s ', __DIR__ . '/../../app/', $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
        ));
        $cmdccw = "cache:warmup";
        passthru(sprintf(
                        'php "%s/console" ' . $cmdccw . ' --env=%s ', __DIR__ . '/../../app/', $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
        ));

        $client = self::createClient();
        $output = $this->runCommand($client, "pannelloamministrazione:generateentities wbadmintest.mwb Fi/ProvaBundle --schemaupdate --env=test");
        echo $output;

        $check = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . "Prova.php";
        $this->assertTrue(file_exists($check));

        $check = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR .
                "doctrine" . DIRECTORY_SEPARATOR . "Prova.orm.yml";
        $this->assertTrue(file_exists($check));
    }

//    public function testPannelloGenerateBundle2()
//    {
//        /* @var $em \Doctrine\ORM\EntityManager */
//        /* @var $reg \Doctrine\Bundle\DoctrineBundle\Registry */
//        //$em = $client->getKernel()->getContainer()->get('doctrine')->getManager();
//        //var_dump($client->getKernel()->registerBundles());exit;
//        //$output = $this->runCommand($client, "container:debug");
//        //echo $output;
//        //$records = $em->getRepository('FiProvaBundle:Prova')->findAll();
////        $client = self::createClient();
////        var_dump(\Fi\CoreBundle\Entity\Permessi::class);
////        exit;
////        $records = $this->createMock(\Fi\ProvaBundle\Entity\Prova::class);
////        $entityManager = $this
////                ->getMockBuilder(ObjectManager::class)
////                ->disableOriginalConstructor()
////                ->getMock();
////
////        $employeeRepository->expects($this->once())
////                ->method('find')
////                ->will($this->returnValue($records));
//        //echo count($records);
//        $client = self::createClient();
//        $client->getKernel()->shutdown();
//        $client->getKernel()->boot();
//        $client->restart();
//        sleep(1);
//
//        $output = $this->runCommand($client, "doctrine:generate:crud --entity=FiProvaBundle:Prova --route-prefix=Prova --with-write --format=yml --overwrite --no-interaction");
//        echo $output;
//
//        $cmd = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\Commands($client->getContainer());
//        $output = $cmd->generateFormCrud("FiProvaBundle", "Prova");
//        var_dump($output);
//    }

    protected function tearDown()
    {
        parent::tearDown();
        startTests();
    }

}

<?php

namespace Fi\CoreBundle\Tests\Command;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;

class PannelloAmministrazioneTest extends CommandTestCase
{

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

        $output = $this->runCommand($client, "doctrine:cache:clear-metadata --env=test");
        echo $output;
        sleep(1);
        $output = $this->runCommand($client, "cache:clear --env=test");
        echo $output;
        sleep(1);
        $output = $this->runCommand($client, "cache:warmup --env=test");
        echo $output;
        sleep(1);

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

        //$output = $this->runCommand($client, "cache:warmup --env=test");
        //echo $output;
        //sleep(1);
    }

//    public function testPannelloGenerateBundle2()
//    {
//        self::bootKernel();
//
//        $this->em = static::$kernel->getContainer()
//                ->get('doctrine')
//                ->getManager();
//        /* @var $em \Doctrine\ORM\EntityManager */
//        /* @var $reg \Doctrine\Bundle\DoctrineBundle\Registry */
//        //$em = $client->getKernel()->getContainer()->get('doctrine')->getManager();
//        //var_dump($client->getKernel()->registerBundles());exit;
//        //$output = $this->runCommand($client, "container:debug");
//        //echo $output;
//        //$records = $em->getRepository('FiProvaBundle:Prova')->findAll();
//
//
//        $records = $this->em
//                        ->getRepository('ProvaBundle:Prova')->findAll();
//        ;
//
//        $this->assertCount(1, $records);
//
//        /* $client = self::createClient();
//          $records = $this->createMock(\Fi\ProvaBundle\Entity\Prova::class);
//          $entityManager = $this
//          ->getMockBuilder(ObjectManager::class)
//          ->disableOriginalConstructor()
//          ->getMock();
//
//          $employeeRepository->expects($this->once())
//          ->method('find')
//          ->will($this->returnValue($records)); */
//
//        //echo count($records);
//        exit;
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
        removecache();
        sleep(2);
    }

}

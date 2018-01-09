<?php

namespace Fi\CoreBundle\Tests\Command;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;

class PannelloAmministrazioneTest extends CommandTestCase
{

    public static $conn;

    public static function setUpBeforeClass()
    {
        cleanFilesystem();
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        //self::bootKernel();
        //$this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        clearcache()
    }

    public function testPannelloGenerateBundle()
    {
        $client = self::createClient();
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($client->getContainer());

        $checkent = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . "Prova.php";

        $checkres = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR .
                "doctrine" . DIRECTORY_SEPARATOR . "Prova.orm.yml";

        $console = __DIR__ . '/../../bin/console';
        /*
          "cache:clear --no-warmup",
          "cache:warmup",
         */
        $listcommands = array(
            "generate:bundle --namespace=Fi/ProvaBundle --dir=src/ --format=yml --no-interaction",
            "doctrine:cache:clear-metadata --flush",
            "cache:clear --no-warmup",
            "cache:warmup",
            "pannelloamministrazione:generateymlentities wbadmintest.mwb Fi/ProvaBundle",
            "pannelloamministrazione:generateentities Fi/ProvaBundle --schemaupdate",
        );

        $megacommand = "";
        foreach ($listcommands as $cmd) {
            $megacommand = $megacommand . "php " . $console . " " . $cmd . " --no-debug --env=test &&";
        }
        $megacommand = substr($megacommand, 0, -3);
        passthru($megacommand);

        //TODO: Ripristinare il test
        $this->assertTrue(file_exists($checkent));
        $this->assertTrue(file_exists($checkres));
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
        cleanFilesystem();
        clearcache();
        parent::tearDown();
    }

}

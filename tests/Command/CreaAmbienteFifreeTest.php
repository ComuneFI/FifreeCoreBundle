<?php

namespace Fi\CoreBundle\Tests\Command;

class GeneraAmbienteTest extends CommandTestCase
{

    public function testGenerateAmbiente()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, 'fifree2:mysqlconvertdbengine INNODB --tablesfifree2 --env=test --no-interaction');
        //echo $output;
        $output = $this->runCommand($client, "fifree2:mysqldropforeignkeys --env=test --no-interaction");
        //echo $output;
        $output = $this->runCommand($client, "fifree2:mysqltruncatetables --tablesfifree2  --env=test --no-interaction");
        //echo $output;
        
        $output = $this->runCommand($client, "fifree2:droptables --force --env=test --no-interaction");
        //echo $output;
        $output = $this->runCommand($client, "fifree2:dropdatabase --force --env=test --no-interaction");
        //echo $output;
        $output = $this->runCommand($client, "fifree2:install admin admin admin@admin.it --env=test");
        //echo $output;
        
        $output = $this->runCommand($client, "fifree2:pubblicamanuale --env=test");
        //echo $output;
        
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

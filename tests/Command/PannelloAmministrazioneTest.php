<?php

namespace Fi\CoreBundle\Tests\Command;

class PannelloAmministrazioneTest extends CommandTestCase
{

    public static function setUpBeforeClass()
    {
        startTests();
    }

//    public function testPannelloGenerateBundle()
//    {
//        $client = self::createClient();
//        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($client->getContainer());
//
//        $output = $this->runCommand($client, "generate:bundle --namespace=Fi/ProvaBundle --dir=src/ --format=yml --env=test --no-interaction");
//        echo $output;
//
//        $client = self::createClient();
//        $output = $this->runCommand($client, "pannelloamministrazione:generateentities wbadmintest.mwb Fi/ProvaBundle --schemaupdate --env=test");
//        echo $output;
//
//        $output = $this->runCommand($client, "cache:clear --env=test");
//        echo $output;
//        $output = $this->runCommand($client, "cache:warmup --env=test");
//        echo $output;
//        
//        $client = self::createClient();
//        /* @var $em \Doctrine\ORM\EntityManager */
//        /* @var $reg \Doctrine\Bundle\DoctrineBundle\Registry */
//        $em = $client->getKernel()->getContainer()->get('doctrine')->getManager();
//        
//        //var_dump($client->getKernel()->registerBundles());exit;
//        //$output = $this->runCommand($client, "container:debug");
//        //echo $output;
//        
//        $records = $em->getRepository('FiProvaBundle:Prova')->findAll();
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

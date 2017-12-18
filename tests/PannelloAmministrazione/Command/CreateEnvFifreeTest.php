<?php

namespace Fi\CoreBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateEnvFifreeTest extends WebTestCase
{

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    public static function setUpBeforeClass()
    {
        cleanFilesystem();
    }

    /**
     * {@inheritDoc}
     */
//    protected function setUp()
//    {
//        //self::bootKernel();
//        //$this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
//    }
//
    public function test10InstallFifree()
    {

        $kernel = $this->createKernel();
        $kernel->boot();

        /* $application = new Application($kernel);
          $application->add(new \Fi\CoreBundle\Command\Fifree2droptablesCommand());

          $command = $application->find('fifree2:droptables');
          $commandTester = new CommandTester($command);
          $commandTester->execute(
          array(
          '--force' => true,
          '--no-interaction' => 'true'
          )
          );

          $this->assertRegExp('/.../', $commandTester->getDisplay());
         */
        $application = new Application($kernel);
        $application->add(new \Fi\CoreBundle\Command\Fifree2dropdatabaseCommand());

        $command = $application->find('fifree2:dropdatabase');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    '--force' => true,
                    '--no-interaction' => true
                )
        );

        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $application = new Application(static::$kernel);
        $application->add(new \Fi\CoreBundle\Command\Fifree2installCommand());
        $application->add(new \Fi\CoreBundle\Command\Fifree2createdatabaseCommand());
        $application->add(new \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand());

        $command = $application->find('fifree2:install');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'admin' => 'admin',
                    'adminpass' => 'admin',
                    'adminemail' => 'admin@admin.it'
                )
        );

        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $container = static::$kernel->getContainer();
        $username4test = $container->getParameter('user4test');
        $em = $container->get('doctrine')->getManager();
        $qb2 = $em->createQueryBuilder();
        $qb2->select(array('a'));
        $qb2->from('FiCoreBundle:Operatori', 'a');
        $qb2->where('a.username = :descrizione');
        $qb2->setParameter('descrizione', $username4test);
        $record2 = $qb2->getQuery()->getResult();
        $recorddelete = $record2[0];
        $this->assertEquals($recorddelete->getUsername(), $username4test);
    }

//    public function test20InstallFifree()
//    {
//        $console = __DIR__ . '/../../bin/console';
//        $cmd = "php " . $console . " fifree2:dropdatabase --force --no-interaction --no-debug --env=test";
//        passthru($cmd);
//        /* $kernel = $this->createKernel();
//          $kernel->boot();
//
//          $application = new Application($kernel);
//          $application->add(new \Fi\CoreBundle\Command\Fifree2dropdatabaseCommand());
//
//          $command = $application->find('fifree2:dropdatabase');
//          $commandTester = new CommandTester($command);
//          $commandTester->execute(
//          array(
//          '--force' => true,
//          '--no-interaction' => true
//          )
//          );
//
//          $this->assertRegExp('/.../', $commandTester->getDisplay()); */
//
//        $console = __DIR__ . '/../../bin/console';
//        $cmd = "php " . $console . " fifree2:install admin admin admin@admin.it --no-debug --env=test";
//        passthru($cmd);
//        /* $application = new Application(static::$kernel);
//          $application->add(new \Fi\CoreBundle\Command\Fifree2installCommand());
//          $application->add(new \Fi\CoreBundle\Command\Fifree2createdatabaseCommand());
//          $application->add(new \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand());
//
//          $command = $application->find('fifree2:install');
//          $commandTester = new CommandTester($command);
//          $commandTester->execute(
//          array(
//          'admin' => 'admin',
//          'adminpass' => 'admin',
//          'adminemail' => 'admin@admin.it'
//          )
//          );
//
//          $this->assertRegExp('/.../', $commandTester->getDisplay()); */
//    }

    public function test30InstallFifree()
    {
//        $console = __DIR__ . '/../../bin/console';
//        $cmd = "php " . $console . " pannelloamministrazione:generateymlentities  wbadmintest.mwb Fi/ProvaBundle --env=test";
//        echo passthru($cmd);
//        $console = __DIR__ . '/../../bin/console';
//        $cmd = "php " . $console . " cache:clear --env=test";
//        echo passthru($cmd);
//        $console = __DIR__ . '/../../bin/console';
//        $cmd = "php " . $console . " pannelloamministrazione:generateentities  Fi/ProvaBundle --schemaupdate --env=test";
//        echo passthru($cmd);
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath(self::$kernel->getContainer());
        $checkent = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . "Prova.php";


        $checkres = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR .
                "doctrine" . DIRECTORY_SEPARATOR . "Prova.orm.yml";
        $application = new Application(static::$kernel);
        /* $application->add(new \Sensio\Bundle\GeneratorBundle\Command\GenerateBundleCommand);
          $command = $application->find('generate:bundle');
          $commandTester = new CommandTester($command);
          $commandTester->execute(
          array(
          '--namespace' => 'Fi',
          '--bundle-name' => 'ProvaBundle',
          '--dir' => 'src/',
          '--format' => 'yml',
          '--no-interaction' => true,
          '--env' => 'test'
          )
          );
          echo $commandTester->getDisplay();
          $this->assertRegExp('/.../', $commandTester->getDisplay()); */


        echo "Generate bundle\n";
        $console = __DIR__ . '/../../bin/console';
        $cmd = "php " . $console . " generate:bundle  --namespace=Fi/ProvaBundle --dir=src/ --no-interaction --no-debug --format=yml  -n --env=test";
        passthru($cmd);
        writestdout("Generated bundle");

        /*

          $console = __DIR__ . '/../../bin/console';
          $cmd = "php " . $console . " pannelloamministrazione:generateymlentities wbadmintest.mwb Fi/ProvaBundle --no-debug --env=test";
          //$cmd = "php " . $console . " pannelloamministrazione:generateymlentities wbadmintest.mwb Fi/ProvaBundle --env=test";
          passthru($cmd);
          writestdout("Generated yml");
         */
        /* $console = __DIR__ . '/../../bin/console';
          $cmd = "php " . $console . " cache:clear --no-debug --env=test";
          passthru($cmd);
          writestdout("Clear cache");

          $console = __DIR__ . '/../../bin/console';
          $cmd = "php " . $console . " doctrine:cache:clear-metadata --no-debug --env=test";
          passthru($cmd);
          writestdout("Clear cache metadata"); */

        /* $console = __DIR__ . '/../../bin/console';
          $cmd = "php " . $console . " pannelloamministrazione:generateentities Fi/ProvaBundle --schemaupdate --no-debug --env=test";
          //$cmd = "php " . $console . " pannelloamministrazione:generateentities Fi/ProvaBundle --schemaupdate --env=test";
          passthru($cmd);
          writestdout("Generated entities"); */

        $kernel = static::$kernel;
        $application = new Application($kernel);
        $application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateymlentitiesCommand());
        $command = $application->find('pannelloamministrazione:generateymlentities');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'mwbfile' => 'wbadmintest.mwb',
                    'bundlename' => 'Fi/ProvaBundle',
                    '--no-debug' => true
                )
        );


        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateentitiesCommand());
        $application->add(new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand());
        $command = $application->find('pannelloamministrazione:generateentities');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'bundlename' => 'Fi/ProvaBundle',
                    '--schemaupdate' => true,
                    '--no-debug' => true
                )
        );

        $this->assertRegExp('/.../', $commandTester->getDisplay());
        //echo $checkent;
        $this->assertTrue(file_exists($checkent));
        $this->assertTrue(file_exists($checkres));
    }

    protected function tearDown()
    {
        parent::tearDown();
        cleanFilesystem();
    }

}

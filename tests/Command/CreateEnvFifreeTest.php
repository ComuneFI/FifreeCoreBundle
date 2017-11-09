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
        startTests();
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
    /* public function test10InstallFifree()
      {

      $kernel = $this->createKernel();
      $kernel->boot();

      $application = new Application($kernel);
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
      } */

    public function test15InstallFifree()
    {

        $kernel = $this->createKernel();
        $kernel->boot();

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
    }

    public function test20InstallFifree()
    {

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
    }

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

        /* $command = $application->find('generate:bundle');
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
          $this->assertRegExp('/.../', $commandTester->getDisplay());
         */



        $console = __DIR__ . '/../../bin/console';
        $cmd = "php " . $console . " generate:bundle  --namespace=Fi/ProvaBundle --dir=src/ --no-interaction --no-debug --format=yml  -n --env=test";
        echo passthru($cmd);

        $kernel = static::$kernel;
        $application = new Application($kernel);
        $application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateymlentitiesCommand());
        $command = $application->find('pannelloamministrazione:generateymlentities');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'mwbfile' => 'wbadmintest.mwb',
                    'bundlename' => 'Fi/ProvaBundle'/* ,
                  '--no-debug' => true */
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
                    '--schemaupdate' => true/* ,
                  '--no-debug' => true */
                )
        );

        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $this->assertTrue(file_exists($checkent));
        $this->assertTrue(file_exists($checkres));
    }

    protected function tearDown()
    {
        parent::tearDown();
        startTests();
    }

}

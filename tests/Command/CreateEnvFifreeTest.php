<?php

namespace Fi\CoreBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateEnvFifreeTest extends KernelTestCase
{

    /*public function test10InstallFifree()
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
    }*/

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

        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new \Fi\CoreBundle\Command\Fifree2installCommand());

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

        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand());

        $console = __DIR__ . '/../../bin/console';
        $cmd = "php " . $console . " generate:bundle --namespace=Fi/ProvaBundle --dir=src/ --format=yml --no-interaction --env=test";
        echo passthru($cmd);

        $command = $application->find('pannelloamministrazione:generateentities');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'mwbfile' => 'wbadmintest.mwb',
                    'bundlename' => 'Fi/ProvaBundle',
                    '--schemaupdate' => true,
                )
        );
        echo $commandTester->getDisplay();exit;
        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($kernel->getContainer());
        $checkent = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . "Prova.php";


        $checkres = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR .
                "doctrine" . DIRECTORY_SEPARATOR . "Prova.orm.yml";

        $this->assertTrue(file_exists($checkent));
        $this->assertTrue(file_exists($checkres));
    }

    /*
      $output = $this->runCommand($client, 'fifree2:mysqlconvertdbengine INNODB --tablesfifree2 --env=test --no-interaction');
      $output = $this->runCommand($client, "fifree2:mysqldropforeignkeys --env=test --no-interaction");
      $output = $this->runCommand($client, "fifree2:mysqltruncatetables --tablesfifree2  --env=test --no-interaction");
      $output = $this->runCommand($client, "fifree2:pubblicamanuale --env=test");
     */

    protected function tearDown()
    {
        parent::tearDown();
        startTests();
    }

}

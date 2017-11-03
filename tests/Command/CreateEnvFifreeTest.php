<?php

namespace Fi\CoreBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateEnvFifreeTest extends KernelTestCase
{

    public function test10InstallFifree()
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
    }

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
                    '--no-interaction' => 'true'
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

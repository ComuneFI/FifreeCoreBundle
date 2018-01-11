<?php

namespace Fi\CoreBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateEnvFifreeTest extends KernelTestCase
{

    static $kernel;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->kernel = static::createKernel();
        $this->kernel->boot();
    }

    public function test10InstallFifree()
    {

        /* $application = new Application($this->kernel);
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
        $application = new Application($this->kernel);
        $application->add(new \Fi\CoreBundle\Command\Fifree2dropdatabaseCommand());

        $command = $application->find('fifree2:dropdatabase');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    '--force' => true,
                    '--no-interaction' => true,
                    '--env' => 'test'
                )
        );

        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $application = new Application($this->kernel);
        $application->add(new \Fi\CoreBundle\Command\Fifree2installCommand());
        $application->add(new \Fi\CoreBundle\Command\Fifree2createdatabaseCommand());
        $application->add(new \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand());

        $command = $application->find('fifree2:install');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'admin' => 'admin',
                    'adminpass' => 'admin',
                    'adminemail' => 'admin@admin.it',
                    '--env' => 'test'
                )
        );

        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $container = $this->kernel->getContainer();
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
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($this->kernel->getContainer());
        $checkent = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . "Prova.php";


        $checkres = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR .
                "doctrine" . DIRECTORY_SEPARATOR . "Prova.orm.yml";
        $application = new Application($this->kernel);
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
        //$cmd = "php " . $console . " generate:bundle  --namespace=Fi/ProvaBundle --dir=src/ --no-interaction --no-debug --format=yml  -n --env=test";
        $cmd = "php " . $console . " generate:bundle  --namespace=Fi/ProvaBundle --dir=src/ --no-interaction --format=yml  -n --env=test";
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

        $application = new Application($this->kernel);
        $application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateymlentitiesCommand());
        $command = $application->find('pannelloamministrazione:generateymlentities');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'mwbfile' => 'wbadmintest.mwb',
                    'bundlename' => 'Fi/ProvaBundle',
                    '--env' => 'test'
                )
        );

        clearcache();
        $this->kernel->boot();
        $this->assertRegExp('/.../', $commandTester->getDisplay());
        $application = new Application($this->kernel);

        $application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateentitiesCommand());
        $application->add(new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand());
        $command = $application->find('pannelloamministrazione:generateentities');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'bundlename' => 'Fi/ProvaBundle',
                    '--schemaupdate' => true,
                    '--env' => 'test'
                )
        );
        $this->assertRegExp('/.../', $commandTester->getDisplay());
        //echo $checkent;
        $this->assertTrue(file_exists($checkent));
        $this->assertTrue(file_exists($checkres));
    }

    protected function tearDown()
    {
        cleanFilesystem();
        clearcache();
        parent::tearDown();
    }

}

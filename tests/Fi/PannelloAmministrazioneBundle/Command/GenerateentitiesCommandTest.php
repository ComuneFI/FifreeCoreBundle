<?php

namespace Fi\CoreBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateentitiesCommandTest extends KernelTestCase
{

    public static function setUpBeforeClass()
    {
        cleanFilesystem();
        removecache();
        clearcache();
    }

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;
    private $application;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->container = $kernel->getContainer();
        $this->em = $kernel->getContainer()
                ->get('doctrine')
                ->getManager();

        $this->application = new Application($kernel);
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
        $this->application->add(new \Fi\CoreBundle\Command\Fifree2dropdatabaseCommand());

        $command = $this->application->find('fifree2:dropdatabase');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    '--force' => true,
                    '--no-interaction' => true,
                    '--env' => 'test'
                )
        );

        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $this->application->add(new \Fi\CoreBundle\Command\Fifree2installCommand());
        $this->application->add(new \Fi\CoreBundle\Command\Fifree2createdatabaseCommand());
        $this->application->add(new \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand());

        $command = $this->application->find('fifree2:install');
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

        $container = $this->application->getKernel()->getContainer();
        $username4test = $container->getParameter('user4test');
        $em = $this->em;
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
//        $console = __DIR__ . '/../../../bin/console';
//        $cmd = "php " . $console . " pannelloamministrazione:generateymlentities  wbadmintest.mwb Fi/ProvaBundle --env=test";
//        echo passthru($cmd);
//        $console = __DIR__ . '/../../../bin/console';
//        $cmd = "php " . $console . " cache:clear --env=test";
//        echo passthru($cmd);
//        $console = __DIR__ . '/../../../bin/console';
//        $cmd = "php " . $console . " pannelloamministrazione:generateentities  Fi/ProvaBundle --schemaupdate --env=test";
//        echo passthru($cmd);
        $container = $this->application->getKernel()->getContainer();
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($container);
        $checkent = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . "Prova.php";


        $checkres = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR .
                "doctrine" . DIRECTORY_SEPARATOR . "Prova.orm.yml";
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
        $console = __DIR__ . '/../../../bin/console';
        //$cmd = "php " . $console . " generate:bundle  --namespace=Fi/ProvaBundle --dir=src/ --no-interaction --no-debug --format=yml  -n --env=test";
        $cmd = "php " . $console . " generate:bundle  --namespace=Fi/ProvaBundle --dir=src/ --no-interaction --format=yml  -n --env=test";
        passthru($cmd);
        writestdout("Generated bundle");

        /*

          $console = __DIR__ . '/../../../bin/console';
          $cmd = "php " . $console . " pannelloamministrazione:generateymlentities wbadmintest.mwb Fi/ProvaBundle --no-debug --env=test";
          //$cmd = "php " . $console . " pannelloamministrazione:generateymlentities wbadmintest.mwb Fi/ProvaBundle --env=test";
          passthru($cmd);
          writestdout("Generated yml");
         */
        /* $console = __DIR__ . '/../../../bin/console';
          $cmd = "php " . $console . " cache:clear --no-debug --env=test";
          passthru($cmd);
          writestdout("Clear cache");

          $console = __DIR__ . '/../../../bin/console';
          $cmd = "php " . $console . " doctrine:cache:clear-metadata --no-debug --env=test";
          passthru($cmd);
          writestdout("Clear cache metadata"); */

        /* $console = __DIR__ . '/../../../bin/console';
          $cmd = "php " . $console . " pannelloamministrazione:generateentities Fi/ProvaBundle --schemaupdate --no-debug --env=test";
          //$cmd = "php " . $console . " pannelloamministrazione:generateentities Fi/ProvaBundle --schemaupdate --env=test";
          passthru($cmd);
          writestdout("Generated entities"); */

        $this->application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateymlentitiesCommand());
        $command = $this->application->find('pannelloamministrazione:generateymlentities');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'mwbfile' => 'wbadmintest.mwb',
                    'bundlename' => 'Fi/ProvaBundle',
                    '--env' => 'test'
                )
        );

        writestdout("Generated yml entities");
        $this->assertRegExp('/.../', $commandTester->getDisplay());
        removecache();
        clearcache();

        $this->application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateentitiesCommand());
        $this->application->add(new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand());
        $command = $this->application->find('pannelloamministrazione:generateentities');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'bundlename' => 'Fi/ProvaBundle',
                    '--schemaupdate' => true,
                    '--env' => 'test'
                )
        );
        writestdout("Generated entities");
        $this->assertRegExp('/.../', $commandTester->getDisplay());
        //echo $checkent;
        $this->assertTrue(file_exists($checkent));
        $this->assertTrue(file_exists($checkres));
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null; // avoid memory leaks
        cleanFilesystem();
        removecache();
        clearcache();
        writestdout("end GenerateentitiesCommandTest");
    }

}

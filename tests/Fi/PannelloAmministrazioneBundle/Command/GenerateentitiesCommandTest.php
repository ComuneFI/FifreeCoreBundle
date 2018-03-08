<?php

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
        cachewarmup();
    }

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    protected $container;
    protected $application;

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
                    '--no-interaction' => true
                )
        );

        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $this->application->add(new \Fi\CoreBundle\Command\Fifree2installCommand());
        $this->application->add(new \Fi\CoreBundle\Command\Fifree2createdatabaseCommand());
        $this->application->add(new \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand());

        clearcache();
        cachewarmup();

        $command = $this->application->find('fifree2:install');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'admin' => 'admin',
                    'adminpass' => 'admin',
                    'adminemail' => 'admin@admin.it'
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

    public function test20GenerateEntity()
    {

        $container = $this->application->getKernel()->getContainer();
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($container);

        $checkent = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . "Prova.php";

        $checkres = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR .
                "doctrine" . DIRECTORY_SEPARATOR . "Prova.orm.yml";

        //dump("Generate bundle");
        $console = __DIR__ . '/../../../../bin/console';
        $cmd = $console . " generate:bundle  --namespace=Fi/ProvaBundle --dir=src/ --no-interaction --format=yml  -n --env=test --no-debug > /dev/null 2>&1";
        passthru($cmd);
        //dump("Generated bundle");

        $this->application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateymlentitiesCommand());
        $command = $this->application->find('pannelloamministrazione:generateymlentities');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'mwbfile' => 'wbadmintest.mwb',
                    'bundlename' => 'Fi/ProvaBundle'
                )
        );

        //dump("Generated yml entities");
        $this->assertRegExp('/.../', $commandTester->getDisplay());
        //dump($commandTester->getDisplay());

        clearcache();
        cachewarmup();

        $this->application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateentitiesCommand());
        $this->application->add(new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand());
        $this->application->add(new Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand());
        $command = $this->application->find('pannelloamministrazione:generateentities');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'bundlename' => 'Fi/ProvaBundle',
                    '--schemaupdate' => true
                )
        );

        /* $cmd = "php " . $console . " doctrine:cache:clear-metadata --env=test --no-debug >> /tmp/generate.log";
          passthru($cmd);
          $cmd = "php " . $console . " doctrine:cache:clear-metadata --flush --env=test --no-debug >> /tmp/generate.log";
          passthru($cmd);
          $cmd = "php " . $console . " doctrine:cache:clear-entity-region Fi\Prova --env=test --no-debug >> /tmp/generate.log";
          passthru($cmd);
          $cmd = "php " . $console . " doctrine:schema:update --force --env=test --no-debug >> /tmp/generate.log";
          passthru($cmd); */

        //dump("Generated entities");
        $this->assertRegExp('/.../', $commandTester->getDisplay());
        //dump($commandTester->getDisplay());

        clearcache();
        cachewarmup();

        $this->assertTrue(file_exists($checkent));
        $this->assertTrue(file_exists($checkres));
        /* Genera form */

        $bundlename = "Fi/ProvaBundle";
        $entityform = "Prova";

        $container = $this->application->getKernel()->getContainer();
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($container);
        $checkform = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Form" . DIRECTORY_SEPARATOR . "ProvaType.php";

        $this->application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateFormCommand());
        $command = $this->application->find('pannelloamministrazione:generateformcrud');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'bundlename' => $bundlename,
                    'entityform' => $entityform,
                    '--env' => 'test',
                    '--no-debug'
                )
        );
        //dump($commandTester->getDisplay());
        //dump("Generated entities");
        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $this->assertTrue(file_exists($checkform));
    }

    protected function tearDown()
    {
        parent::tearDown();
        cleanFilesystem();
        removecache();
        clearcache();
        cachewarmup();
    }

}

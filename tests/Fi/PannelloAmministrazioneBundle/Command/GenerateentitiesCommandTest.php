<?php

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateentitiesCommandTest extends KernelTestCase
{
    public static function setUpBeforeClass() : void
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
    protected function setUp() : void
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

        $command = $this->application->find('fifree2:droptables');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    '--force' => true,
                    '--no-interaction' => true
                )
        );

        $this->assertRegExp('/.../', $commandTester->getDisplay());

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

        $checkent = $apppath->getSrcPath() . "/Entity/Prova.php";

        $checkres = $apppath->getSrcPath() . "/../config/doctrine/Prova.orm.yml";

        $this->application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateymlentitiesCommand());
        $command = $this->application->find('pannelloamministrazione:generateymlentities');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'mwbfile' => 'wbadmintest.mwb'
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

        $entityform = 'Prova';
        $container = $this->application->getKernel()->getContainer();
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($container);
        $checkform = $apppath->getSrcPath() . "/Form/ProvaType.php";

        $this->application->add(new \Fi\PannelloAmministrazioneBundle\Command\GenerateFormCommand());
        $command = $this->application->find('pannelloamministrazione:generateformcrud');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'entityform' => $entityform
                )
        );
        //dump($commandTester->getDisplay());
        //dump("Generated entities");
        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $this->assertTrue(file_exists($checkform));
    }
    protected function tearDown() : void
    {
        parent::tearDown();
        cleanFilesystem();
        removecache();
        clearcache();
        cachewarmup();
    }
}

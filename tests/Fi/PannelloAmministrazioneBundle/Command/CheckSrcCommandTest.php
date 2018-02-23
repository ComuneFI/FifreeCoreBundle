<?php

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CheckSrcCommandTest extends KernelTestCase
{

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

    public function testCheckSrc()
    {
        $this->application->add(new \Fi\PannelloAmministrazioneBundle\Command\ChecksrcCommand);

        $command = $this->application->find('pannelloamministrazione:checksrc');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array());

        $this->assertRegExp('/.../', $commandTester->getDisplay());
    }

}

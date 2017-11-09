<?php

namespace Fi\CoreBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class OperatoriCommandTest extends WebTestCase
{

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    public function testAddOperatore()
    {
        $userprova = 'utenteprova';
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $application->add(new \FOS\UserBundle\Command\CreateUserCommand());
        $application->add(new \FOS\UserBundle\Command\ActivateUserCommand());

        $command = $application->find('fos:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'username' => $userprova,
                    'email' => $userprova . '@domain.it',
                    'password' => 'passwordprova',
                    '--inactive' => true
                )
        );
        $output = $commandTester->getDisplay();

        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository('FiCoreBundle:Operatori')->findOneBy(array(
            'username' => $userprova,
        ));

        $this->assertContains('Created user', $output);
        $this->assertEquals($userprova . '@domain.it', $user->getEmail());
        $this->assertEquals(false, $user->isEnabled());

        $command = $application->find('fos:user:activate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'username' => $userprova
                )
        );
        $output = $commandTester->getDisplay();
        $this->assertRegExp('/.../', $commandTester->getDisplay());

        $this->assertContains('has been activated', $output);
        $this->assertEquals($userprova . '@domain.it', $user->getEmail());
        $this->assertEquals(true, $user->isEnabled());

        $em->remove($user);
        $em->flush();
    }

}

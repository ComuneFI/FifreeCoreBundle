<?php

namespace Fi\CoreBundle\Tests\Command;

class OperatoriCommandTest extends CommandTestCase
{
    public function testAddOperatore()
    {
        $userprova = 'utenteprova';
        $client = self::createClient();
        $output = $this->runCommand($client, "fos:user:create $userprova $userprova@domain.it passwordprova --inactive");

        $em = $client->getKernel()->getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository('FiCoreBundle:Operatori')->findOneBy(array(
            'username' => $userprova,
        ));

        $this->assertContains('Created user', $output);
        $this->assertEquals($userprova.'@domain.it', $user->getEmail());
        $this->assertEquals(false, $user->isEnabled());

        $output = $this->runCommand($client, "fos:user:activate $userprova");

        $this->assertContains('has been activated', $output);
        $this->assertEquals($userprova.'@domain.it', $user->getEmail());
        $this->assertEquals(true, $user->isEnabled());

        $em->remove($user);
        $em->flush();
    }
}

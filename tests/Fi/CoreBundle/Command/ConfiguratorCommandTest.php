<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ConfiguratorCommandTest extends WebTestCase
{

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    public function testConfigurator()
    {
        $entity = 'Permessi';
        $fixturefile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "fixtures.yml";
        @unlink($fixturefile);
        /* $console = __DIR__ . '/../../bin/console';
          $cmd = "php " . $console . " fos:user:create $userprova $userprova@domain.it passwordprova --no-debug --env=test";
          passthru($cmd); */
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $application->add(new \Fi\CoreBundle\Command\Fifree2configuratorexportCommand());
        $application->add(new \Fi\CoreBundle\Command\Fifree2configuratorimportCommand());

        $commandimport = $application->find('fifree2:configuratorimport');
        $commandTesterImport = new CommandTester($commandimport);
        $commandTesterImport->execute(array('--forceupdate' => true, '--verboso' => true));
        $outputimport = $commandTesterImport->getDisplay();

        $this->assertRegExp('/.../', $outputimport);
        $this->assertContains('Non trovato file ' . $fixturefile, $outputimport);

        $commandexport = $application->find('fifree2:configuratorexport');
        $commandTesterExport = new CommandTester($commandexport);
        $commandTesterExport->execute(array());
        $outputexport = $commandTesterExport->getDisplay();

        $this->assertRegExp('/.../', $outputexport);
        $this->assertContains('Export Entity: Fi\\CoreBundle\\Entity\\' . $entity, $outputexport);


        /* Rimuovo ruolo Utente per generare l'inserimento tramite import */
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository('FiCoreBundle:Ruoli')->findOneBy(array(
            'ruolo' => "Utente",
        ));

        $em->remove($user);
        $em->flush();

        $admin = $em->getRepository('FiCoreBundle:Ruoli')->findOneBy(array(
            'ruolo' => "Amministratore",
        ));

        $admin->setRuolo("Amministratores");
        $em->persist($admin);
        $em->flush();

        $permesso = $em->getRepository('FiCoreBundle:Permessi')->findOneBy(array(
            'modulo' => "Ffprincipale",
        ));
        $permesso->setRuoli($admin);
        $em->persist($permesso);
        $em->flush();

        /**/
        $operatore = $em->getRepository('FiCoreBundle:Operatori')->findOneBy(array(
            'username' => "admin",
        ));
        $operatore->setLastLogin(new \DateTime());
        $operatore->setRoles(array("ROLE_SUPER_ADMIN", "ROLE_ADMIN", "ROLE_UNDEFINED"));
        $em->persist($operatore);
        $em->flush();
        /**/

        $commandTesterImport2 = new CommandTester($commandimport);
        $commandTesterImport2->execute(array('--forceupdate' => true, '--verboso' => true));
        $outputimport2 = $commandTesterImport2->getDisplay();
        $this->assertNotContains('Non trovato file ' . $fixturefile, $outputimport2);
        $this->assertContains('Modifica', $outputimport2);
        $this->assertContains('ROLE_UNDEFINED', $outputimport2);

        $user = $em->getRepository('FiCoreBundle:Ruoli')->findOneBy(array(
            'ruolo' => "Utente",
        ));
        $this->assertTrue($user->getRuolo() === 'Utente');

        $commandTesterImport3 = new CommandTester($commandimport);
        $commandTesterImport3->execute(array('--forceupdate' => true, '--verboso' => true, '--truncatetables' => true));
        $outputimport3 = $commandTesterImport3->getDisplay();
        //echo $outputimport3;exit;
        $this->assertNotContains('Non trovato file ' . $fixturefile, $outputimport3);
        $this->assertContains('aggiunta', $outputimport3);
        $this->assertContains('tramite entity find', $outputimport3);
        $this->assertContains(' in formato Boolean', $outputimport3);
        $this->assertContains('in formato DateTime', $outputimport3);

        unlink($fixturefile);
    }
}

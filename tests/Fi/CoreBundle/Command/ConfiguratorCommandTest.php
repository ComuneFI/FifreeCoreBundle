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
        $entity = 'OpzioniTabella';
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
        $commandTesterExport->execute(
                array(
                    'entity' => $entity
                )
        );
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
        $em->clear();


        $commandTesterImport2 = new CommandTester($commandimport);
        $commandTesterImport2->execute(array('--forceupdate' => true, '--verboso' => true));
        $outputimport2 = $commandTesterImport2->getDisplay();
        //echo $outputimport2;exit;
        $this->assertNotContains('Non trovato file ' . $fixturefile, $outputimport2);
        $this->assertContains('aggiunta', $outputimport2);
        unlink($fixturefile);
        $user = $em->getRepository('FiCoreBundle:Ruoli')->findOneBy(array(
            'ruolo' => "Utente",
        ));
        $this->assertTrue($user->getRuolo() === 'Utente');
    }

}

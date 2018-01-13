<?php

namespace Fi\PannelloAmministrazioneBundle\Tests\Dependencyinjection;

use Fi\CoreBundle\DependencyInjection\FifreeTestUtil;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Fi\PannelloAmministrazioneBundle\DependencyInjection\PannelloAmministrazioneUtils;

class PannelloamministrazionedependencyinjectionTest extends FifreeTestUtil
{
    /*
     * @test
     */

    public function test10Dependencyinjection()
    {
        $client = $this->getClientAutorizzato();
        $container = $client->getContainer();
        $lock = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\LockSystem($container);
        $this->assertFalse($lock->isLockedFile());
        $lock->lockFile(true);
        $this->assertTrue($lock->isLockedFile());
        $lock->forceCleanLockFile();
        $this->assertFalse($lock->isLockedFile());
        $this->assertContains("running.run", $lock->getFileLock());
    }

}

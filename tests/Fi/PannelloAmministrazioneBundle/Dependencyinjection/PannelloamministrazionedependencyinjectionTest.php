<?php

use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class PannelloamministrazionedependencyinjectionTest extends FifreeTestAuthorizedClient
{
    /*
     * @test
     */

    public function test10Dependencyinjection()
    {
        $client = $this->getClient();
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

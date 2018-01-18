<?php

namespace Fi\CoreBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Fi\PannelloAmministrazioneBundle\DependencyInjection\Commands;

class CommandsTest extends WebTestCase
{

    public function testCommands()
    {
        $client = static::createClient();
        $cmds = new Commands($client->getContainer());
        $this->expectExceptionMessage("Vcs non trovato");
        $cmds->getVcs();
    }

}

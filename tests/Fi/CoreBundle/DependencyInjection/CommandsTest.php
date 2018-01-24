<?php

namespace Fi\CoreBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommandsTest extends WebTestCase
{

    public function testCommands()
    {
        $client = static::createClient();
        $cmds = $client->getContainer()->get("pannelloamministrazione.commands");
        $this->expectExceptionMessage("Vcs non trovato");
        $cmds->getVcs();
    }

}

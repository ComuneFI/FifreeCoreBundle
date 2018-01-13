<?php

namespace Fi\CoreBundle\Tests\Command;

use Fi\CoreBundle\DependencyInjection\FifreeTestUtil;

class PannelloAmministrazioneTest extends FifreeTestUtil
{

    public static $conn;

    public static function setUpBeforeClass()
    {
        cleanFilesystem();
        clearcache();
    }
    
    public function testPannelloGenerateBundle()
    {
        $client = $this->getClientAutorizzato();
        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($client->getContainer());

        $checkent = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . "Prova.php";

        $checkres = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . "Fi" . DIRECTORY_SEPARATOR . "ProvaBundle" .
                DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR .
                "doctrine" . DIRECTORY_SEPARATOR . "Prova.orm.yml";

        $console = __DIR__ . '/../../../bin/console';
        /*
          "cache:clear --no-warmup",
          "cache:warmup",
         */
        $listcommands = array(
            "generate:bundle --namespace=Fi/ProvaBundle --dir=src/ --format=yml --no-interaction",
            "doctrine:cache:clear-metadata --flush",
            "cache:clear --no-warmup",
            /*"cache:warmup",*/
            "pannelloamministrazione:generateymlentities wbadmintest.mwb Fi/ProvaBundle",
            "pannelloamministrazione:generateentities Fi/ProvaBundle --schemaupdate",
        );

        $megacommand = "";
        foreach ($listcommands as $cmd) {
            //$megacommand = $megacommand . "php " . $console . " " . $cmd . " --no-debug --env=test &&";
            $megacommand = $megacommand . "php " . $console . " " . $cmd . " --env=test &&";
        }
        $megacommand = substr($megacommand, 0, -3);
        passthru($megacommand);

        //TODO: Ripristinare il test
        $this->assertTrue(file_exists($checkent));
        $this->assertTrue(file_exists($checkres));
    }

    protected function tearDown()
    {
        cleanFilesystem();
        clearcache();
        parent::tearDown();
    }

}

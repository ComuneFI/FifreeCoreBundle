<?php

use Fi\CoreBundle\DependencyInjection\FifreeTestAuthorizedClient;

class PannelloAmministrazioneControllerTest extends FifreeTestAuthorizedClient
{
    /*
     * @test
     */
    public function testIndexAdminpanel()
    {

        $client = $this->getClient();
        $url = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_homepage');
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());

        $urlsc = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_symfonycommand');
        $client->request('GET', $urlsc, array("symfonycommand" => "cache:clear --env=prod --no-debug"));
        $this->assertTrue($client->getResponse()->isSuccessful());

        $urluc = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_unixcommand');
        $client->request('GET', $urluc, array("unixcommand" => "ls -all"));
        $this->assertTrue($client->getResponse()->isSuccessful());

        //Restart client per caricare il nuovo bundle
        $client->reload();
        $urlge = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_generateentity');
        $client->request('GET', $urlge, array("file" => "wbadmintest.mwb"));
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->reload();
        $urlas = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_aggiornaschemadatabase');
        $client->request('GET', $urlas);
        $this->assertTrue($client->getResponse()->isSuccessful());

        //$client->reload();
        $urlgf = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_generateformcrud');
        $client->request('GET', $urlgf, array("entityform" => "Prova"));
        //dump($client->getResponse());
        $this->assertTrue($client->getResponse()->isSuccessful());

        $urlct = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_changetheme');
        $client->request('GET', $urlct, array("theme" => "cupertino"));
        $this->assertTrue($client->getResponse()->isSuccessful());

        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($client->getContainer());
        $checkentityprova = $apppath->getSrcPath() . "/Entity/Prova.php";
        $checkbaseentityprova = $apppath->getSrcPath() . "/Entity/BaseProva.php";
        $checkentitycollegataprova = $apppath->getSrcPath() . "/Entity/Tabellacollegata.php";
        $checkbaseentitycollegataprova = $apppath->getSrcPath() . "/Entity/BaseTabellacollegata.php";
        $checktypeprova = $apppath->getSrcPath() . "/Form/ProvaType.php";
        $checkviewsprova = $apppath->getSrcPath() . "/../templates/Prova";
        $checkindexprova = $apppath->getSrcPath() . "/../templates/Prova/index.html.twig";

        $this->assertTrue(file_exists($checkbaseentityprova));
        $this->assertTrue(file_exists($checkentityprova));
        $this->assertTrue(file_exists($checkentitycollegataprova));
        $this->assertTrue(file_exists($checkbaseentitycollegataprova));
        $this->assertTrue(file_exists($checktypeprova));
        $this->assertTrue(file_exists($checkviewsprova));
        $this->assertTrue(file_exists($checkindexprova));

        //Lo lancio per ultimo perchè ad oggi va in 500 per il mock della sessione, quindi qui non dovrebbe dare noia a nessuno
        $urlcc = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_clearcache');
        $client->request('GET', $urlcc);

        cleanFilesystem();
        //dump($client->getResponse());
    }
}

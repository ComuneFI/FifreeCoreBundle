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
        //$this->assertContains('DoctrineORMEntityManager', get_class($em));

        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());

        /* $urlcc = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_clearcache');
          $client->request('GET', $urlcc, array("env"=>"prod"));
          dump($client->getResponse());exit;
          $this->assertTrue($client->getResponse()->isSuccessful()); */

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
        $urlgc = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_generateentityclass');
        $client->request('GET', $urlgc, array());
        $this->assertTrue($client->getResponse()->isSuccessful());

        $urlas = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_aggiornaschemadatabase');
        $client->request('GET', $urlas);
        $this->assertTrue($client->getResponse()->isSuccessful());

        //$client->reload();
        $urlgf = $client->getContainer()->get('router')->generate('fi_pannello_amministrazione_generateformcrud');
        $client->request('GET', $urlgf, array("entityform" => "Prova"));
        //dump($client->getResponse());
        $this->assertTrue($client->getResponse()->isSuccessful());

        $apppath = new \Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath($client->getContainer());
        $checkentityprova = $apppath->getSrcPath() . "/App/Entity/Prova.php";
        $checkresourceprova = $apppath->getSrcPath() . "/App/Resources/config/doctrineProva.orm.yml";
        $checktypeprova = $apppath->getSrcPath() . "/App/Form/ProvaType.php";
        $checkviewsprova = $apppath->getSrcPath() . "App/tempolates/Prova";
        $checkindexprova = $apppath->getSrcPath() . "App/tempolates/Prova/index.html.twig";

        $this->assertTrue(file_exists($checkresourceprova));
        $this->assertTrue(file_exists($checkentityprova));
        $this->assertTrue(file_exists($checktypeprova));
        $this->assertTrue(file_exists($checkviewsprova));
        $this->assertTrue(file_exists($checkindexprova));

        cleanFilesystem();
        //dump($client->getResponse());
    }
}

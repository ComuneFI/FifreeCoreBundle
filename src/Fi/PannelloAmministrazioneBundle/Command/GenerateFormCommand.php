<?php

namespace Fi\PannelloAmministrazioneBundle\Command;

use Fi\CoreBundle\Entity\Permessi;
use Fi\CoreBundle\Entity\Tabelle;
use Fi\OsBundle\DependencyInjection\OsFunctions;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GenerateFormCommand extends ContainerAwareCommand
{

    protected $apppaths;
    protected $genhelper;
    protected $pammutils;

    protected function configure()
    {
        $this
                ->setName('pannelloamministrazione:generateformcrud')
                ->setDescription('Genera le views per il crud')
                ->setHelp('Genera le views per il crud, <br/>fifree.mwb Fi/CoreBundle default [--schemaupdate]<br/>')
                ->addArgument('entityform', InputArgument::REQUIRED, 'Il nome entity del form da creare');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);
        $this->apppaths = $this->getContainer()->get("pannelloamministrazione.projectpath");
        $pammutils = $this->getContainer()->get("pannelloamministrazione.utils");

        $bundlename = "App";
        $entityform = $input->getArgument('entityform');

        $phpPath = OsFunctions::getPHPExecutableFromPath();
        $command = $phpPath . ' ' . $this->apppaths->getConsole() . ' --env=dev make:form ';
        $resultcrud = $pammutils->runCommand($command . $entityform . "Type" . " " . $entityform);
        if ($resultcrud['errcode'] == 0) {
            $fs = new Filesystem();
            //Controller
            $controlleFile = $this->apppaths->getSrcPath() . '/Controller/' . $entityform . 'Controller.php';
            $code = $this->getControllerCode(str_replace('/', '\\', $bundlename), $entityform);
            $fs->dumpFile($controlleFile, $code);
            $output->writeln("<info>Creato " . $controlleFile . "</info>");

            //Routing
            $retmsg = $this->generateFormRouting($bundlename, $entityform);
            //Twig template (Crea i template per new edit show)
            $this->generateFormWiew($bundlename, $entityform, 'edit');
            $this->generateFormWiew($bundlename, $entityform, 'index');
            $this->generateFormWiew($bundlename, $entityform, 'new');

            $this->generateFormsDefaultTableValues($entityform);
            $output->writeln("<info>" . $retmsg . "</info>");
            return 0;
        } else {
            $output->writeln("<error>" . $resultcrud['errmsg'] . "</error>");
            return 1;
        }
    }
    private function generateFormRouting($bundlename, $entityform)
    {
        //Routing del form
        $fs = new Filesystem();
        $routingFile = $this->apppaths->getSrcPath() . '/../config/routes/' . strtolower($entityform) . '.yml';

        $code = $this->getRoutingCode(str_replace('/', '', $bundlename), $entityform);
        $fs->dumpFile($routingFile, $code);

        //Fixed: Adesso questa parte la fa da solo symfony (05/2015)
        //Refixed dalla versione 2.8 non lo fa più (04/2016)

        $dest = $this->apppaths->getSrcPath() . '/../config/routes.yaml';

        $routingContext = str_replace('/', '', $bundlename) . '_' . $entityform . ':' . "\n" .
                '  resource: routes/' . strtolower($entityform) . '.yml' . "\n" .
                '  prefix: /' . $entityform . "\n\n";

        //Si fa l'append nel file routing del bundle per aggiungerci le rotte della tabella che stiamo gestendo
        $fh = file_get_contents($dest);
        if ($fh !== false) {
            file_put_contents($dest, $routingContext . $fh);
            $retmsg = 'Routing ' . $dest . " generato automaticamente da pannelloammonistrazionebundle\n\n* * * * CLEAR CACHE * * * *\n";
        } else {
            $retmsg = 'Impossibile generare il ruoting automaticamente da pannelloammonistrazionebundle\n';
        }

        return $retmsg;
    }
    private function generateFormWiew($bundlename, $entityform, $view)
    {
        $fs = new Filesystem();
        $folderview = $this->apppaths->getSrcPath() . '/../templates/' . $entityform . DIRECTORY_SEPARATOR;
        $dest = $folderview . $view . '.html.twig';
        $fs->mkdir($folderview);
        file_put_contents($dest, "{% include 'FiCoreBundle:Standard:" . $view . ".html.twig' %}");
    }
    private function generateFormsDefaultTableValues($entityform)
    {
        //Si inserisce il record di default nella tabella permessi
        $em = $this->getContainer()->get('doctrine')->getManager();
        $ruoloAmm = $em->getRepository('FiCoreBundle:Ruoli')->findOneBy(array('is_superadmin' => true)); //SuperAdmin

        $newPermesso = new Permessi();
        $newPermesso->setCrud('crud');
        $newPermesso->setModulo($entityform);
        $newPermesso->setRuoli($ruoloAmm);
        $em->persist($newPermesso);
        $em->flush();

        $tabelle = new Tabelle();
        $tabelle->setNometabella($entityform);
        $em->persist($tabelle);
        $em->flush();
    }
    private function getControllerCode($bundlename, $tabella)
    {
        $codeTemplate = <<<EOF
<?php
namespace [bundle]\Controller;

use Fi\CoreBundle\Controller\FiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Fi\CoreBundle\Controller\Griglia;
use [bundle]\Entity\[tabella];
use [bundle]\Form\[tabella]Type;


/**
* [tabella] controller.
*
*/

class [tabella]Controller extends FiController {

}
EOF;
        $codebundle = str_replace('[bundle]', $bundlename, $codeTemplate);
        $code = str_replace('[tabella]', $tabella, $codebundle);

        return $code;
    }
    private function getRoutingCode($bundlename, $tabella)
    {
        $codeTemplate = <<<'EOF'
[tabella]_container:
    path:  /
    defaults: { _controller: '[bundle]\Controller\[tabella]Controller::indexAction' }

[tabella]_new:
    path:  /new
    defaults: { _controller: '[bundle]\Controller\[tabella]Controller::newAction' }

[tabella]_create:
    path:  /create
    defaults: { _controller: '[bundle]\Controller\[tabella]Controller::createAction' }
    requirements: { methods: post }

[tabella]_edit:
    path:  /{id}/edit
    defaults: { _controller: '[bundle]\Controller\[tabella]Controller::editAction' }

[tabella]_update:
    path:  /{id}/update
    defaults: { _controller: '[bundle]\Controller\[tabella]Controller::updateAction' }
    requirements: { methods: post|put }

[tabella]_aggiorna:
    path:  /aggiorna
    defaults: { _controller: '[bundle]\Controller\[tabella]Controller::aggiornaAction' }
    requirements: { methods: post|put }

[tabella]_delete:
    path:  /{id}/delete
    defaults: { _controller: '[bundle]\Controller\[tabella]Controller::deleteAction' }
    requirements: { methods: post|delete }

[tabella]_deletemultiple:
    path:  /delete
    defaults: { _controller: '[bundle]\Controller\[tabella]Controller::deleteAction' }
    requirements: { methods: post|delete }

[tabella]_griglia:
    path:  /griglia
    defaults: { _controller: '[bundle]\Controller\[tabella]Controller::GrigliaAction' }
    requirements: { methods: get|post }
EOF;
        $codebundle = str_replace('[bundle]', $bundlename, $codeTemplate);
        $code = str_replace('[tabella]', $tabella, $codebundle);

        return $code;
    }
}

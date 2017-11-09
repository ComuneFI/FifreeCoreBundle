<?php

namespace Fi\PannelloAmministrazioneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Fi\OsBundle\DependencyInjection\OsFunctions;
use Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath;
use Fi\PannelloAmministrazioneBundle\DependencyInjection\PannelloAmministrazioneUtils;
use Fi\PannelloAmministrazioneBundle\DependencyInjection\GeneratorHelper;

class GenerateymlentitiesCommand extends ContainerAwareCommand
{

    protected $apppaths;
    protected $genhelper;
    protected $pammutils;

    protected function configure()
    {
        $this
                ->setName('pannelloamministrazione:generateymlentities')
                ->setDescription('Genera le entities partendo da un modello workbeanch mwb')
                ->setHelp('Genera i ifle yml per le entities partendo da un modello workbeanch mwb, <br/>fifree.mwb Fi/CoreBundle default<br/>')
                ->addArgument('mwbfile', InputArgument::REQUIRED, 'Nome file mwb, fifree.mwb')
                ->addArgument('bundlename', InputArgument::REQUIRED, 'Nome del bundle, Fi/CoreBundle')
                ->addArgument('em', InputArgument::OPTIONAL, 'Entity manager, default = default');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);
        $this->apppaths = new ProjectPath($this->getContainer());
        $this->genhelper = new GeneratorHelper($this->getContainer());
        $this->pammutils = new PannelloAmministrazioneUtils($this->getContainer());

        $bundlename = $input->getArgument('bundlename');
        $mwbfile = $input->getArgument('mwbfile');

        if (!$input->getArgument('em')) {
            $emdest = 'default';
        } else {
            $emdest = $input->getArgument('em');
        }

        $wbFile = $this->apppaths->getDocPath() . DIRECTORY_SEPARATOR . $mwbfile;
        $checkprerequisiti = $this->genhelper->checkprerequisiti($bundlename, $mwbfile, $output);

        if ($checkprerequisiti < 0) {
            return -1;
        }

        $destinationPath = $this->genhelper->getDestinationEntityYmlPath($bundlename);

        $command = $this->getExportJsonCommand($bundlename, $wbFile);

        $schemaupdateresult = PannelloAmministrazioneUtils::runCommand($command);
        if ($schemaupdateresult["errcode"] < 0) {
            $output->writeln($schemaupdateresult["errmsg"]);
            return 1;
        } else {
            $output->writeln($schemaupdateresult["errmsg"]);
        }

        $this->genhelper->removeExportJsonFile();

        $tablecheck = $this->genhelper->checktables($destinationPath, $wbFile, $output);

        if ($tablecheck < 0) {
            return 1;
        }

        $output->writeln('<info>Entities yml create</info>');
        /* $generateentitiesresult = $this->pammutils->clearCache();
          if ($generateentitiesresult["errcode"] < 0) {
          $output->writeln($generateentitiesresult["errmsg"]);
          return 1;
          } else {
          $output->writeln($generateentitiesresult["errmsg"]);
          } */
        return 0;
    }

    private function getExportJsonCommand($bundlePath, $wbFile)
    {
        $exportJson = $this->genhelper->getExportJsonFile();
        $scriptGenerator = $this->genhelper->getScriptGenerator();
        $destinationPathEscaped = str_replace('/', "\/", str_replace('\\', '/', $this->genhelper->getDestinationEntityYmlPath($bundlePath)));
        $bundlePathEscaped = str_replace('\\', '\\\\', str_replace('/', '\\', $bundlePath));

        $exportjsonfile = GeneratorHelper::getJsonMwbGenerator();

        $bundlejson = str_replace('[bundle]', str_replace('/', '', $bundlePathEscaped), $exportjsonfile);
        $exportjsonreplaced = str_replace('[dir]', $destinationPathEscaped, $bundlejson);
        file_put_contents($exportJson, $exportjsonreplaced);
        $sepchr = OsFunctions::getSeparator();
        if (OsFunctions::isWindows()) {
            $command = 'cd ' . $this->apppaths->getRootPath() . $sepchr
                    . $scriptGenerator . '.bat --export=doctrine2-yaml '
                    . ' --config=' .
                    $exportJson . ' ' . $wbFile . ' ' . $destinationPathEscaped;
        } else {
            $phpPath = OsFunctions::getPHPExecutableFromPath();
            $command = 'cd ' . $this->apppaths->getRootPath() . $sepchr
                    . $phpPath . ' ' . $scriptGenerator . ' --export=doctrine2-yaml '
                    . ' --config=' .
                    $exportJson . ' ' . $wbFile . ' ' . $destinationPathEscaped;
        }

        return $command;
    }
}

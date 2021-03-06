<?php

namespace Fi\PannelloAmministrazioneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fi\OsBundle\DependencyInjection\OsFunctions;

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
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);
        $this->apppaths = $this->getContainer()->get("pannelloamministrazione.projectpath");
        $this->genhelper = $this->getContainer()->get("pannelloamministrazione.generatorhelper");
        $this->pammutils = $this->getContainer()->get("pannelloamministrazione.utils");
        $mwbfile = $input->getArgument('mwbfile');

        $wbFile = $this->apppaths->getDocPath() . DIRECTORY_SEPARATOR . $mwbfile;
        $checkprerequisiti = $this->genhelper->checkprerequisiti($mwbfile, $output);

        if ($checkprerequisiti < 0) {
            return -1;
        }

        $destinationPath = $this->genhelper->getDestinationEntityYmlPath();

        $command = $this->getExportJsonCommand($wbFile);
        
        $schemaupdateresult = $this->pammutils->runCommand($command);
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
        return 0;
    }
    private function getExportJsonCommand($wbFile)
    {
        $exportJson = $this->genhelper->getExportJsonFile();
        $scriptGenerator = $this->genhelper->getScriptGenerator();
        $destinationPathEscaped = $this->genhelper->getDestinationEntityYmlPath();
        $exportjsonfile = $this->genhelper->getJsonMwbGenerator();
        
        $exportjsonreplaced = str_replace('[dir]', $destinationPathEscaped, $exportjsonfile);
        
        file_put_contents($exportJson, $exportjsonreplaced);
        $sepchr = OsFunctions::getSeparator();
        if (OsFunctions::isWindows()) {
            $command = 'cd ' . $this->apppaths->getRootPath() . $sepchr
                    . $scriptGenerator . '.bat '
                    . ' --config=' .
                    $exportJson . ' ' . $wbFile . ' ' . $destinationPathEscaped;
        } else {
            $phpPath = OsFunctions::getPHPExecutableFromPath();
            $command = 'cd ' . $this->apppaths->getRootPath() . $sepchr
                    . $phpPath . ' ' . $scriptGenerator . ' '
                    . ' --config=' .
                    $exportJson . ' ' . $wbFile . ' ' . $destinationPathEscaped;
        }

        return $command;
    }
}

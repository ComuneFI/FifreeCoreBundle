<?php

namespace Fi\PannelloAmministrazioneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Fi\OsBundle\DependencyInjection\OsFunctions;

class GenerateentitiesCommand extends ContainerAwareCommand
{

    protected $apppaths;
    protected $genhelper;
    protected $pammutils;

    protected function configure()
    {
        $this
                ->setName('pannelloamministrazione:generateentities')
                ->setDescription('Genera le entities partendo da un modello workbeanch mwb')
                ->setHelp('Genera le entities partendo da un modello workbeanch mwb, <br/>fifree.mwb Fi/CoreBundle default [--schemaupdate]<br/>')
                ->addArgument('bundlename', InputArgument::REQUIRED, 'Nome del bundle, Fi/CoreBundle')
                ->addArgument('em', InputArgument::OPTIONAL, 'Entity manager, default = default')
                ->addOption('schemaupdate', null, InputOption::VALUE_NONE, 'Se settato fa anche lo schema update sul db');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);
        $this->apppaths = $this->getContainer()->get("pannelloamministrazione.projectpath");
        $this->genhelper = $this->getContainer()->get("pannelloamministrazione.generatorhelper");
        $this->pammutils = $this->getContainer()->get("pannelloamministrazione.utils");

        $bundlename = $input->getArgument('bundlename');
        $schemaupdate = false;

        if (!$input->getArgument('em')) {
            $emdest = 'default';
        } else {
            $emdest = $input->getArgument('em');
        }

        if ($input->getOption('schemaupdate')) {
            $schemaupdate = true;
        }

        /* $generateentitiesresult = $this->pammutils->clearCache();
          if ($generateentitiesresult["errcode"] < 0) {
          $output->writeln($generateentitiesresult["errmsg"]);
          return 1;
          } else {
          $output->writeln($generateentitiesresult["errmsg"]);
          } */

        $generatecheck = $this->generateentities($bundlename, $emdest, $schemaupdate, $output);
        if ($generatecheck < 0) {
            return 1;
        }

        return 0;
    }

    private function generateentities($bundlename, $emdest, $schemaupdate, $output)
    {
        /* GENERATE ENTITIES */
        $output->writeln('Creazione entities class per il bundle ' . str_replace('/', '', $bundlename));

        $console = $this->apppaths->getConsole();
        $scriptGenerator = $console . ' doctrine:generate:entities';
        $phpPath = OsFunctions::getPHPExecutableFromPath();

        $command = $phpPath . ' ' . $scriptGenerator . ' --no-backup ' . str_replace('/', '', $bundlename)
                . ' --env=' . $this->getContainer()->get('kernel')->getEnvironment();

        $generateentitiesresult = $this->pammutils->runCommand($command);
        if ($generateentitiesresult["errcode"] < 0) {
            $output->writeln($generateentitiesresult["errmsg"]);
            return 1;
        } else {
            $output->writeln($generateentitiesresult["errmsg"]);
        }

        $output->writeln('<info>Entities class create</info>');

        if ($schemaupdate) {
            $output->writeln('Aggiornamento database...');

            $scriptGenerator = $console . ' doctrine:schema:update';

            $phpPath = OsFunctions::getPHPExecutableFromPath();
            $command = $phpPath . ' ' . $scriptGenerator . ' --force --em=' . $emdest
                    . ' --no-debug --env=' . $this->getContainer()->get('kernel')->getEnvironment();

            $schemaupdateresult = $this->pammutils->runCommand($command);
            if ($schemaupdateresult["errcode"] < 0) {
                $output->writeln($schemaupdateresult["errmsg"]);
            } else {
                $output->writeln($schemaupdateresult["errmsg"]);
                $output->writeln('<info>Aggiornamento database completato</info>');
            }
            return $schemaupdateresult["errcode"];
        }
    }
}

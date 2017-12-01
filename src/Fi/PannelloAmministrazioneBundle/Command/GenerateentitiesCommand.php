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
        $this->apppaths = new ProjectPath($this->getContainer());
        $this->genhelper = new GeneratorHelper($this->getContainer());
        $this->pammutils = new PannelloAmministrazioneUtils($this->getContainer());

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
        //$application = new Application($this->getContainer()->get('kernel'));
        //$application->setAutoExit(false);

        $console = $this->apppaths->getConsole();
        $scriptGenerator = $console . ' doctrine:generate:entities';
        $phpPath = OsFunctions::getPHPExecutableFromPath();

        $command = $phpPath . ' ' . $scriptGenerator . ' --no-backup ' . str_replace('/', '', $bundlename)
                . ' --env=' . $this->getContainer()->get('kernel')->getEnvironment();

        $generateentitiesresult = PannelloAmministrazioneUtils::runCommand($command);
        if ($generateentitiesresult["errcode"] < 0) {
            $output->writeln($generateentitiesresult["errmsg"]);
            return 1;
        } else {
            $output->writeln($generateentitiesresult["errmsg"]);
        }

        /* $command = $this->getApplication()->find('doctrine:generate:entities');
          $inputdge = new ArrayInput(array('--no-backup' => true, 'name' => str_replace('/', '', $bundlename)));
          $command->run($inputdge, $output); */

        $output->writeln('<info>Entities class create</info>');

        if ($schemaupdate) {
            $output->writeln('Aggiornamento database...');

            /* $command = $this->getApplication()->find('doctrine:schema:update');
              $inputdsu = new ArrayInput(array('--force' => true, '--em' => $emdest));
              $result = $command->run($inputdsu, $output); */

            $scriptGenerator = $console . ' doctrine:schema:update';

            $phpPath = OsFunctions::getPHPExecutableFromPath();
            $command = $phpPath . ' ' . $scriptGenerator . ' --force --em=' . $emdest
                    . ' --no-debug --env=' . $this->getContainer()->get('kernel')->getEnvironment();


            $schemaupdateresult = PannelloAmministrazioneUtils::runCommand($command);
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

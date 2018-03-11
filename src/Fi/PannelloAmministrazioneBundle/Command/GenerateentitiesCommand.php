<?php

namespace Fi\PannelloAmministrazioneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Fi\OsBundle\DependencyInjection\OsFunctions;
use Symfony\Component\Filesystem\Filesystem;

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
                ->addArgument('em', InputArgument::OPTIONAL, 'Entity manager, default = default')
                ->addOption('schemaupdate', null, InputOption::VALUE_NONE, 'Se settato fa anche lo schema update sul db');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);
        $this->apppaths = $this->getContainer()->get("pannelloamministrazione.projectpath");
        $this->genhelper = $this->getContainer()->get("pannelloamministrazione.generatorhelper");
        $this->pammutils = $this->getContainer()->get("pannelloamministrazione.utils");

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
        $this->prepare();
        $generatecheck = $this->generateentities($emdest, $schemaupdate, $output);
        $this->finish();
        if ($generatecheck < 0) {
            return 1;
        }

        return 0;
    }
    private function prepare()
    {
        $fs = new Filesystem();
        $path = $this->apppaths->getSrcPath();
        $fs->mkdir($path . "/App");
    }
    private function finish()
    {
        $fs = new Filesystem();
        $path = $this->apppaths->getSrcPath();
        //$fs->remove($path . "/App");
        
    }
    private function generateentities($emdest, $schemaupdate, $output)
    {
        /* GENERATE ENTITIES */
        $output->writeln('Creazione entities class');

        $console = $this->apppaths->getConsole();
        $scriptGenerator = $console . ' doctrine:generate:entities';
        $phpPath = OsFunctions::getPHPExecutableFromPath();

        //$command = $phpPath . ' ' . $scriptGenerator . ' --no-backup --path=' . $this->apppaths->getSrcPath() . "/App App";
        $command = $phpPath . ' ' . $scriptGenerator . ' --no-backup';

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

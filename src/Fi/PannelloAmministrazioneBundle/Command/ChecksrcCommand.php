<?php

namespace Fi\PannelloAmministrazioneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ChecksrcCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('pannelloamministrazione:checksrc')
                ->setDescription('Controlla i sorgenti')
                ->setHelp('Usa phpcs, phpmd, ecc per controllare il codice in src');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$container = $this->getContainer();
        $vendorBin = getcwd() . "/vendor/bin/";


        /* phpcs */
        $phpcscmd = $vendorBin . "phpcs --standard=tools/phpcs/ruleset.xml  --extensions=php src";
        $phpcsoutput = $this->runcmd($phpcscmd);
        if (!$phpcsoutput) {
            $output->writeln("phpcs: OK");
        } else {
            $output->writeln($phpcsoutput);
            $output->writeln("Per correggere automaticamente un file eseguire:");
            $output->writeln($vendorBin . "phpcbf --standard=PSR2 nomefile.php");
        }
        /* phpcs */

        /* phpmd */
        $phpmdcmd = $vendorBin . "phpmd src text tools/phpmd/ruleset.xml";
        $phpmdoutput = $this->runcmd($phpmdcmd);
        if (!$phpmdoutput) {
            $output->writeln("phpmd: OK");
        } else {
            $output->writeln($phpmdoutput);
        }
        /* phpmd */

        /* phpmd */
        $phpcpdcmd = $vendorBin . "phpcpd src";
        $phpcpdoutput = $this->runcmd($phpcpdcmd);
        if (!$phpcpdoutput) {
            $output->writeln("phpmd: OK");
        } else {
            if (strpos($phpcpdoutput, "0.00%")) {
                $output->writeln("phpmd: OK");
            } else {
                $output->writeln($phpcpdoutput);
            }
        }
        /* phpcpd */
    }

    private function runcmd($cmd)
    {
        $process = new Process($cmd);
        $process->setTimeout(60 * 100);
        $process->run();
        $out = "";
        if ($process->isSuccessful()) {
            $out = $process->getOutput();
        } else {
            $out = ($process->getErrorOutput() ? $process->getErrorOutput() : $process->getOutput());
        }
        return $out;
    }
}

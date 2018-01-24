<?php

namespace Fi\PannelloAmministrazioneBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process;
use Fi\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath;
use Fi\OsBundle\DependencyInjection\OsFunctions;

class PannelloAmministrazioneUtils
{

    private $container;
    private $apppaths;

    public function __construct($container)
    {
        $this->container = $container;
        $this->apppaths = $container->get("pannelloamministrazione.projectpath");
    }

    public function clearcache($env = "")
    {
        if (!$env) {
            $env = $this->container->get('kernel')->getEnvironment();
        }

        $phpPath = OsFunctions::getPHPExecutableFromPath();

        if (in_array($env, array('dev', 'test', 'localhost'))) {
            $command = $phpPath . ' ' . $this->apppaths->getConsole() . ' cache:clear '
                    . '--no-warmup --env=' . $env;
        } else {
            $command = $phpPath . ' ' . $this->apppaths->getConsole() . ' cache:clear '
                    . '--no-debug --no-warmup --env=' . $env;

            $command = $command . " && " . $phpPath . ' ' . $this->apppaths->getConsole() . ' cache:warmup '
                    . '--no-debug --env=' . $env;
        }

        return self::runCommand($command);
    }

    public static function runCommand($command)
    {
        /* @var $process \Symfony\Component\Process\Process */
        $return = array();
        $process = new Process($command);
        $process->setTimeout(60 * 100);
        $process->run();

        if (!$process->isSuccessful()) {
            $return = array("errcode" => -1,
                "command" => $command,
                "errmsg" => 'Errore nel comando ' . $command . $process->getErrorOutput() . $process->getOutput());
        } else {
            $return = array("errcode" => 0,
                "command" => $command,
                "errmsg" => $process->getOutput()
            );
        }

        return $return;
    }

    public function runSymfonyCommand($command, array $options = array())
    {
        $application = new Application($this->container->get('kernel'));
        $application->setAutoExit(false);

        $cmdoptions = array_merge(array('command' => $command), $options);

        $outputbuf = new BufferedOutput();
        // return the output, don't use if you used NullOutput()
        $returncode = $application->run(new ArrayInput($cmdoptions), $outputbuf);
        $output = $outputbuf->fetch();

        return array('errcode' => ($returncode == 0 ? false : true), 'command' => $cmdoptions['command'], 'message' => $output);
    }
}

<?php

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;

set_time_limit(0);

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists(Application::class)) {
    throw new \RuntimeException('You need to add "symfony/framework-bundle" as a Composer dependency.');
}

if (!isset($_SERVER['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    (new Dotenv())->load(__DIR__ . '/../tests/.env');
}

require __DIR__ . '/Utils/FifreeTestAuthorizedClient.php';
require __DIR__ . '/Utils/FifreeTestUnauthorizedClient.php';
require __DIR__ . '/Utils/FifreeUserTestUtil.php';
require __DIR__ . '/Utils/CommandTestCase.php';
//require __DIR__ . '/Utils/CoreMinkDriver.php';
require __DIR__ . '/Utils/FacebookDriverTester.php';
require __DIR__ . '/Utils/StartServers.php';

date_default_timezone_set('Europe/Rome');

function clearcache()
{
    passthru(sprintf(
                    '"%s/console" cache:clear', __DIR__ . '/../bin'
    ));
}

// More bootstrap code

function cachewarmup()
{
    passthru(sprintf(
                    '"%s/console" cache:warmup', __DIR__ . '/../bin'
    ));
    #sleep(1);
}

function databaseinit()
{
    passthru(sprintf(
                    '"%s/console" fifree:dropdatabase --force', __DIR__ . '/../bin'
    ));
    passthru(sprintf(
                    '"%s/console" fifree:install admin admin admin@admin.it', __DIR__ . '/../bin'
    ));

    #sleep(1);
}

function removecache()
{
    $vendorDir = dirname(dirname(__FILE__));
    $envs = ["test", "dev", "prod"];
    foreach ($envs as $env) {
        $cachedir = $vendorDir . '/tests/var/cache/' . $env;
        if (file_exists($cachedir)) {
            $command = 'rm -rf ' . $cachedir;
            $process = new Process($command);
            $process->setTimeout(60 * 100);
            $process->run();
            if (!$process->isSuccessful()) {
                echo getErrorText($process, $command);
            } else {
                echo $process->getOutput();
            }
        } else {
            //echo $testcache . " not found";
        }
    }
}

function getErrorText($process, $command)
{
    $error = ($process->getErrorOutput() ? $process->getErrorOutput() : $process->getOutput());

    return 'Errore nel comando ' . $command . ' ' . $error . ' ';
}

function cleanFilesystem()
{
    $vendorDir = dirname(dirname(__FILE__) . '/tests');
    //deleteLineFromFile($kernelfile, $DELETE);
    $routingfile = $vendorDir . '/config/routes.yaml';
    
    $line = fgets(fopen($routingfile, 'r'));
    if (substr($line, 0, -1) == 'App_Prova:') {
        for ($index = 0; $index < 3; ++$index) {
            deleteFirstLineFile($routingfile);
        }
    }

    //$configfile = $vendorDir . '/app/config/config.yml';
    //$remove = '- { resource: "@FiProvaBundle/Resources/config/services.yml" }';
    //deleteLineFromFile($configfile, $remove);

    $fs = new Filesystem();

    $entityfile = $vendorDir . "/src/Entity/Prova.php";

    if ($fs->exists($entityfile)) {
        $fs->remove($entityfile);
    }
    $routingfile = $vendorDir . "/config/routes/prova.yml";

    if ($fs->exists($routingfile)) {
        $fs->remove($routingfile);
    }
    $ormyml = $vendorDir . "/config/doctrine/Prova.orm.yml";

    if ($fs->exists($ormyml)) {
        $fs->remove($ormyml);
    }
    $resources = $vendorDir . "/templates/Prova";
    if ($fs->exists($resources)) {
        $fs->remove($resources, true);
    }

    $form = $vendorDir . "/src/Form/ProvaType.php";
    if ($fs->exists($form)) {
        $fs->remove($form, true);
    }

    $controller = $vendorDir . "/src/Controller/ProvaController.php";
    if ($fs->exists($controller)) {
        $fs->remove($controller, true);
    }
    /* $bundlesrcdir = $vendorDir . '/src';

      if ($fs->exists($bundlesrcdir)) {
      $fs->remove($bundlesrcdir, true);
      } */
}

function deleteFirstLineFile($file)
{
    $handle = fopen($file, 'r');
    fgets($handle, 2048); //get first line.
    $outfile = 'temp';
    $o = fopen($outfile, 'w');
    while (!feof($handle)) {
        $buffer = fgets($handle, 2048);
        fwrite($o, $buffer);
    }
    fclose($handle);
    fclose($o);
    rename($outfile, $file);
}

function deleteLineFromFile($file, $DELETE)
{
    $data = file($file);

    $out = array();

    foreach ($data as $line) {
        if (trim($line) != $DELETE) {
            $out[] = $line;
        }
    }

    $fp = fopen($file, 'w+');
    flock($fp, LOCK_EX);
    foreach ($out as $line) {
        fwrite($fp, $line);
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

function writestdout($buffer)
{
    fwrite(STDOUT, print_r($buffer . "\n", TRUE));
}

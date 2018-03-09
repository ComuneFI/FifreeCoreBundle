<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;

require __DIR__ . '/app/autoload.php';
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
    $testcache = $vendorDir . '/tests/var/cache/test';
    if (file_exists($testcache)) {
        $command = 'rm -rf ' . $testcache;
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
function getErrorText($process, $command)
{
    $error = ($process->getErrorOutput() ? $process->getErrorOutput() : $process->getOutput());

    return 'Errore nel comando ' . $command . ' ' . $error . ' ';
}
function cleanFilesystem()
{
    $vendorDir = dirname(dirname(__FILE__) . '/tests');
    $kernelfile = $vendorDir . '/app/Kernel.php';
    //deleteLineFromFile($kernelfile, $DELETE);
    $routingfile = $vendorDir . '/app/config/routes.yaml';
    $line = fgets(fopen($routingfile, 'r'));
    if (substr($line, 0, -1) == 'fi_provabundle:') {
        for ($index = 0; $index < 4; ++$index) {
            deleteFirstLineFile($routingfile);
        }
    }

    //$configfile = $vendorDir . '/app/config/config.yml';
    //$remove = '- { resource: "@FiProvaBundle/Resources/config/services.yml" }';
    //deleteLineFromFile($configfile, $remove);


    $bundledir = $vendorDir . '/src/Fi/ProvaBundle';

    $fs = new Filesystem();
    if ($fs->exists($bundledir)) {
        $fs->remove($bundledir);
    }

    $bundletestdir = $vendorDir . '/tests';

    if ($fs->exists($bundletestdir)) {
        $fs->remove($bundletestdir, true);
    }
    $bundlesrcdir = $vendorDir . '/src';

    if ($fs->exists($bundlesrcdir)) {
        $fs->remove($bundlesrcdir, true);
    }
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

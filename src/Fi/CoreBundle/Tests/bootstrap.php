<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Fi\OsBundle\DependencyInjection\OsFunctions;

$file = __DIR__.'/../../../../vendor/autoload.php';
if (!file_exists($file)) {
    $file = __DIR__.'/../../../../../../vendor/autoload.php';
    if (!file_exists($file)) {
        throw new RuntimeException('Install dependencies to run test suite.');
    }
}

function startTests()
{
    clearcache();

    cleanFilesystem();
}

function clearcache()
{
    $vendorDir = dirname(dirname(__FILE__)).'/../../../';

    $command = 'rm -rf '.$vendorDir.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'test';
    $process = new Process($command);
    $process->setTimeout(60 * 100);
    $process->run();
    if (!$process->isSuccessful()) {
        echo 'Errore nel comando '.$command.' '.($process->getErrorOutput() ? $process->getErrorOutput() : $process->getOutput()).' ';
    } else {
        echo $process->getOutput();
    }

    $command = 'rm -rf '.$vendorDir.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'dev';
    $process = new Process($command);
    $process->setTimeout(60 * 100);
    $process->run();
    if (!$process->isSuccessful()) {
        echo 'Errore nel comando '.$command.' '.($process->getErrorOutput() ? $process->getErrorOutput() : $process->getOutput()).' ';
    } else {
        echo $process->getOutput();
    }

    if (OsFunctions::isWindows()) {
        $phpPath = OsFunctions::getPHPExecutableFromPath();
    } else {
        $phpPath = '/usr/bin/php';
    }

    $command = $phpPath.' '.$vendorDir.'app'.DIRECTORY_SEPARATOR.'console cache:clear --env=test';
    $process = new Process($command);
    $process->setTimeout(60 * 100);
    $process->run();
    if (!$process->isSuccessful()) {
        echo 'Errore nel comando '.$command.' '.($process->getErrorOutput() ? $process->getErrorOutput() : $process->getOutput()).' ';
    } else {
        echo $process->getOutput();
    }

    $command = $phpPath.' '.$vendorDir.'app'.DIRECTORY_SEPARATOR.'console cache:clear --env=dev';
    $process = new Process($command);
    $process->setTimeout(60 * 100);
    $process->run();
    if (!$process->isSuccessful()) {
        echo 'Errore nel comando '.$command.' '.($process->getErrorOutput() ? $process->getErrorOutput() : $process->getOutput()).' ';
    } else {
        echo $process->getOutput();
    }
}

function cleanFilesystem()
{
    $DELETE = "new Fi\ProvaBundle\FiProvaBundle(),";
    $vendorDir = dirname(dirname(__FILE__)).'/../../../';
    $kernelfile = $vendorDir.'/app/AppKernel.php';
    deleteLineFromFile($kernelfile, $DELETE);
    $routingfile = $vendorDir.'/app/config/routing.yml';
    $line = fgets(fopen($routingfile, 'r'));
    if (substr($line, 0, -1) == 'fi_prova:') {
        for ($index = 0; $index < 4; ++$index) {
            deleteFirstLineFile($routingfile);
        }
    }
    $bundledir = $vendorDir.'/src/Fi/ProvaBundle';

    $fs = new Filesystem();
    if ($fs->exists($bundledir)) {
        $fs->remove($bundledir);
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

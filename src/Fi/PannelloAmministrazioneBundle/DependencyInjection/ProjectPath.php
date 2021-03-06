<?php

namespace Fi\PannelloAmministrazioneBundle\DependencyInjection;

use Exception;

class ProjectPath
{

    /**
     * La funzione ritorna un array con i path dell'applicazione.
     *
     * @param $container Container dell'applicazione
     *
     * @return array Ritorna l'array contenente i path
     */
    private $container;
    private $rootdir;
    private $prjdir;

    public function __construct($container)
    {
        $this->container = $container;
        $rootdir = dirname($this->container->get('kernel')->getRootDir());
        $this->rootdir = $rootdir;
        $this->prjdir = $rootdir;
    }

    public function getRootPath()
    {
        return $this->rootdir;
    }

    public function getProjectPath()
    {
        return $this->prjdir;
    }

    public function getBinPath()
    {
        $bindir = $this->getProjectPath() . '/bin';
        if (!file_exists($bindir)) {
            $bindir = $this->getProjectPath() . '/../bin';
        }
        if (!file_exists($bindir)) {
            $bindir = $this->getProjectPath() . DIRECTORY_SEPARATOR . 'vendor' .
                    DIRECTORY_SEPARATOR . 'bin';
        }
        if (!file_exists($bindir)) {
            throw new Exception("Cartella Bin non trovata", -100);
        }
        return $bindir;
    }

    public function getVendorBinPath()
    {
        $vendorbindir = $this->getProjectPath() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'bin';
        if (!file_exists($vendorbindir)) {
            $vendorbindir = $this->getProjectPath() . '/../vendor/bin';
            if (!file_exists($vendorbindir)) {
                throw new Exception("Cartella Bin in vendor non trovata", -100);
            }
        }
        return $vendorbindir;
    }

    public function getSrcPath()
    {
        $srcdir = $this->getProjectPath() . DIRECTORY_SEPARATOR . 'src';
        return $srcdir;
    }

    public function getAppPath()
    {
        $appdir = $this->getProjectPath() . DIRECTORY_SEPARATOR . 'app';
        return $appdir;
    }

    public function getVarPath()
    {
        $vardir = $this->getProjectPath() . DIRECTORY_SEPARATOR . 'var';
        return $vardir;
    }

    public function getDocPath()
    {
        $docdir = $this->getProjectPath() . DIRECTORY_SEPARATOR . 'doc';
        return $docdir;
    }

    public function getCachePath()
    {
        $cachedir = $this->container->get('kernel')->getCacheDir();
        if (!file_exists($cachedir)) {
            throw new Exception("Cache non trovata", -100);
        }
        return $cachedir;
    }

    public function getLogsPath()
    {
        $logsdir = $this->container->get('kernel')->getLogDir();
        if (!file_exists($logsdir)) {
            throw new Exception("Logs non trovata", -100);
        }
        return $logsdir;
    }

    public function getConsole()
    {
        $console = $this->getBinPath() . '/console';
        // Questo codice per versioni che usano un symfony 2 o 3
        if (!file_exists($console)) {
            $console = $this->getProjectPath() . '/bin/console';
            if (!file_exists($console)) {
                $console = $this->getProjectPath() . '/../bin/console';
            }
            if (!file_exists($console)) {
                $console = $this->getProjectPath() . DIRECTORY_SEPARATOR . 'vendor' .
                        DIRECTORY_SEPARATOR . 'bin/console';
            }
        }
        if (!file_exists($console)) {
            throw new Exception("Console non trovata", -100);
        }
        return $console;
    }
}

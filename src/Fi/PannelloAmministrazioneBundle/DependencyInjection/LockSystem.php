<?php

namespace Fi\PannelloAmministrazioneBundle\DependencyInjection;

use Symfony\Component\HttpFoundation\Response;

class LockSystem
{

    private $container;
    private $apppath;

    public function __construct($container)
    {
        $this->container = $container;
        $this->apppath = $container->get("pannelloamministrazione.projectpath");
    }

    public function getFileLock()
    {
        $cachedir = $this->apppath->getCachePath();
        return $cachedir . DIRECTORY_SEPARATOR . 'running.run';
    }

    public function isLockedFile()
    {
        return file_exists($this->getFileLock());
    }

    public function lockFile($lockstate)
    {
        if ($lockstate) {
            file_put_contents($this->getFileLock(), 0777);
        } else {
            unlink($this->getFileLock());
        }
    }

    public function lockedFunctionMessage()
    {
        $msg = "<h2 style='color: orange;'>E' già in esecuzione un comando, riprova tra qualche secondo!</h2>";
        return new Response($msg);
    }

    public function forceCleanLockFile()
    {
        $this->LockFile(false);
    }
}

<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Process\Process;

class FiVersioneController extends Controller
{

    public static $versione;

    public static function versione($container)
    {

        if (self::isWindows()) {
            return '';
        }

        if (self::$versione) {
            $risposta = self::$versione;
        } else {
            $projectDir = substr($container->get('kernel')->getRootDir(), 0, -4);

            $cmd = 'cd ' . $projectDir;
            $process = new Process($cmd . ';git describe --tags');
            $process->setTimeout(60 * 100);
            $process->run();
            if ($process->isSuccessful()) {
                $out = explode(chr(10), $process->getOutput());

                $version = isset($out[0]) ? $out[0] : "0";

                $risposta = $version;
            } else {
                $risposta = "0";
            }
            self::$versione = $risposta;
        }


        return $risposta;
    }

    public static function isWindows()
    {
        if (PHP_OS == 'WINNT') {
            return true;
        } else {
            return false;
        }
    }
}

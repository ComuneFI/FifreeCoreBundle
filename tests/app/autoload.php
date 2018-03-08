<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Dotenv\Dotenv;

/**
 * @var $loader ClassLoader
 */
$loader = require __DIR__ . '/../../vendor/autoload.php';

if (!isset($_SERVER['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    (new Dotenv())->load(__DIR__ . '/../../.env');
}

require __DIR__ . '/Kernel.php';


AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;

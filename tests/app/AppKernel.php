<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Fi\CoreBundle\FiCoreBundle(),
            new Fi\PannelloAmministrazioneBundle\PannelloAmministrazioneBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test', 'localhost'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }
        
        if ('test' === $this->getEnvironment()) {
            $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            $currentDir = dirname(dirname(__FILE__)) . '/';
            if (file_exists($currentDir . 'src' . DIRECTORY_SEPARATOR . 'Fi' . DIRECTORY_SEPARATOR . 'ProvaBundle')) {
                $bundles[] = new Fi\ProvaBundle\FiProvaBundle();
            }
        }
    
        return $bundles;
    }

    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/logs/' . $this->environment;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__) . '/var/cache/' . $this->environment;
    }


    public function getBinDir()
    {
        return dirname(__DIR__) . '/bin';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

}

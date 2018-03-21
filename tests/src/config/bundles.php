<?php

$bundles = [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\WebServerBundle\WebServerBundle::class => ['dev' => true, 'test' => true],
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle::class => ['all' => true],
    FOS\UserBundle\FOSUserBundle::class => ['all' => true],
    Sensio\Bundle\DistributionBundle\SensioDistributionBundle::class => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class => ['all' => true],
    Fi\CoreBundle\FiCoreBundle::class => ['all' => true],
    Fi\PannelloAmministrazioneBundle\PannelloAmministrazioneBundle::class => ['all' => true],
    Fi\AppBundle\AppBundle::class => ['all' => true],
];
$currentDir = dirname(dirname(__FILE__)) . '/../';

if (file_exists($currentDir . 'src' . DIRECTORY_SEPARATOR . 'Fi' . DIRECTORY_SEPARATOR . 'ProvaBundle')) {
    $bundles[Fi\ProvaBundle\FiProvaBundle::class] = ['test' => true];
}

return $bundles;

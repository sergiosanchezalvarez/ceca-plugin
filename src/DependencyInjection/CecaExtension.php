<?php

declare(strict_types=1);

namespace Sergiosanchezalvarez\CecaPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class CecaExtension
 * @package Sergiosanchezalvarez\CecaPlugin\DependencyInjection
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
final class CecaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        //$loader->load('routing.yml');
    }
}

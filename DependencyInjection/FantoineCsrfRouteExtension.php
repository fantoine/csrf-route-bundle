<?php

namespace Fantoine\CsrfRouteBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Description of FantoineCsrfRouteExtension
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class FantoineCsrfRouteExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load(version_compare(Kernel::VERSION, '2.4.0', '<') ? 'services_23.xml' : 'services_24.xml');
    }
}

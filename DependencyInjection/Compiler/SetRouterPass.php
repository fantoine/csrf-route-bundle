<?php

namespace Fantoine\CsrfRouteBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of SetRouterPass
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class SetRouterPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // Replace default router
        if ($container->hasAlias('router')) {
            // Set parent router
            $container
                ->findDefinition('fantoine_csrf_route.routing.router')
                ->addMethodCall('setParentRouter', [
                    new Reference((string) $container->getAlias('router'))
                ])
            ;
            
            // Update alias
            $container->setAlias('router', 'fantoine_csrf_route.routing.router');
        }
        
        // Replace Sensio Route annotation loader
        $container
            ->findDefinition('sensio_framework_extra.routing.loader.annot_class')
            ->setClass('%fantoine_csrf_route.routing.loader.class%')
        ;
    }
}

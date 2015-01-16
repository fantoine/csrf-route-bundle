<?php

namespace Fantoine\CsrfRouteBundle;

use Fantoine\CsrfRouteBundle\DependencyInjection\Compiler\SetRouterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Description of FantoineCsrfRouteBundle
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class FantoineCsrfRouteBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SetRouterPass());
    }
}

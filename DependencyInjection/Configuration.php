<?php

namespace Fantoine\CsrfRouteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Description of Configuration
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fantoine_csrf_route');

        $rootNode
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->scalarNode('field_name')->defaultValue('_token')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

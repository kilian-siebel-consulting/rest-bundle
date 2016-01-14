<?php

namespace Ibrows\RestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ibrows_rest');

        $rootNode
            ->children()
                ->arrayNode('resources')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('class')->end()
                            ->scalarNode('identifier')
                                ->defaultValue('id')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('param_converter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('fail_on_validation_error')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('listener')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('exclusion_policy')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')
                                    ->defaultFalse()
                                ->end()
                                ->scalarNode('param_name')
                                    ->defaultValue('expolicy')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('debug')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')
                                    ->defaultFalse()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

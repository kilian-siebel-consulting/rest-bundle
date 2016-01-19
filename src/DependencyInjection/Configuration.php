<?php

namespace Ibrows\RestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 *
 * @codeCoverageIgnore
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
                            ->scalarNode('singular_name')->end()
                            ->scalarNode('plural_name')->end()
                            ->scalarNode('class')->end()
                            ->scalarNode('converter')->end()
                            ->scalarNode('identifier')
                                ->defaultValue('id')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('caches')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->integerNode('timetolife')->defaultValue(3600)->end()
                            ->enumNode('type')->values(array('private', 'public', 'nocache', 'nostore'))->end()
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
                    ->arrayNode('collection_decorator')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enabled')
                                ->defaultFalse()
                            ->end()
                        ->end()
                    ->end()
                        ->arrayNode('debug')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')
                                    ->defaultFalse()
                                ->end()
                                ->scalarNode('key_name')
                                    ->defaultValue('_debug')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('decorator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('paginated')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('page_parameter_name')
                                    ->defaultValue('page')
                                ->end()
                                ->scalarNode('limit_parameter_name')
                                    ->defaultValue('limit')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('offset')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('offset_parameter_name')
                                    ->defaultValue('offset')
                                ->end()
                                ->scalarNode('limit_parameter_name')
                                    ->defaultValue('limit')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('last_id')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('sort_by_parameter_name')
                                    ->defaultValue('sortBy')
                                ->end()
                                ->scalarNode('sort_direction_parameter_name')
                                    ->defaultValue('sortDir')
                                ->end()
                                ->scalarNode('offset_id_parameter_name')
                                ->defaultValue('offsetId')
                                ->end()
                                ->scalarNode('limit_parameter_name')
                                    ->defaultValue('limit')
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

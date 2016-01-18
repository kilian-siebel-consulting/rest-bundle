<?php
namespace Ibrows\RestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ResourceTransformerCompilerPass
 * @package Ibrows\RestBundle\DependencyInjection\Compiler
 *
 * @codeCoverageIgnore
 */
class ResourceTransformerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('ibrows_rest.resource_transformer');

        if(!$definition) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds(
            'ibrows_rest.resource_transformer.converter'
        );

        foreach ($taggedServices as $id => $tags) {
            foreach($tags as $tag) {
                if(!isset($tag['converter'])) {
                    continue;
                }
                $definition->addMethodCall(
                    'addConverter',
                    [
                        $tag['converter'],
                        new Reference($id)
                    ]
                );
            }
        }
    }
}

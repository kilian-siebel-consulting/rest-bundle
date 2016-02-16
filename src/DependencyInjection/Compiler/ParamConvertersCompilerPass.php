<?php

namespace Ibrows\RestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ParamConvertersCompilerPass
 * @package Ibrows\RestBundle\DependencyInjection\Compiler
 *
 * @codeCoverageIgnore
 */
class ParamConvertersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $ids = [
            'ibrows_rest.param_converter.link',
            'ibrows_rest.param_converter.unlink',
        ];

        $taggedServices = $container->findTaggedServiceIds(
            'request.param_converter'
        );

        foreach ($ids as $id) {
            if (!$container->has($id)) {
                continue;
            }
            $definition = $container->getDefinition($id);

            foreach ($taggedServices as $id => $tags) {
                foreach ($tags as $tag) {
                    if (!isset($tag['converter'])) {
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
}

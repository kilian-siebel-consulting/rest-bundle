<?php

namespace Ibrows\RestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class PatchOperationAppliersCompilerPass
 * @package Ibrows\RestBundle\DependencyInjection\Compiler
 *
 * @codeCoverageIgnore
 */
class PatchOperationAppliersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(
            'ibrows_rest.patch.operation_applier'
        );

        if (!$container->has('ibrows_rest.patch.executioner.plain')) {
            return;
        }
        $definition = $container->getDefinition('ibrows_rest.patch.executioner.plain');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                if (!array_key_exists('operation', $tag)) {
                    continue;
                }
                $definition->addMethodCall(
                    'addOperationApplier',
                    [
                        $tag['operation'],
                        new Reference($id),
                        $tag['priority'],
                    ]
                );
            }
        }
    }
}

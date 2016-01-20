<?php
namespace Ibrows\RestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DebugViewResponseListenerCompilerPass
 * @package Ibrows\RestBundle\DependencyInjection\Compiler
 *
 * @codeCoverageIgnore
 */
class DebugResponseListenerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ibrows_rest.listener.debug')) {
            return;
        }

        $definition = $container->findDefinition('ibrows_rest.listener.debug');

        $taggedServices = $container->findTaggedServiceIds(
            'ibrows_rest.listener.view_debug.converter'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addConverter',
                [
                    new Reference($id)
                ]
            );
        }
    }
}

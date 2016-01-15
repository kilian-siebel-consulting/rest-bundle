<?php
namespace Ibrows\RestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class DebugViewResponseListenerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('ibrows_rest.listener.view_debug');

        if(!$definition) {
            return;
        }

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

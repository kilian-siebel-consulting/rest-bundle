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
class CollectionDecorationListenerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ibrows_rest.listener.collection_decorator')) {
            return;
        }

        $definition = $container->findDefinition('ibrows_rest.listener.collection_decorator');

        $taggedServices = $container->findTaggedServiceIds(
            'ibrows_rest.collection_decorator'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addDecorator',
                [
                    new Reference($id)
                ]
            );
        }
    }
}

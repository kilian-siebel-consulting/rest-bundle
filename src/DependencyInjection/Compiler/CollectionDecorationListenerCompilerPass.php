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
        $definition = $container->findDefinition('ibrows_rest.listener.collection');

        if(!$definition) {
            return;
        }

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

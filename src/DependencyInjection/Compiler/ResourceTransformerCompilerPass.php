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
        if (!$container->hasDefinition('ibrows_rest.resource_transformer')) {
            return;
        }

        $definition = $container->findDefinition('ibrows_rest.resource_transformer');

        foreach ($container->getParameter('ibrows_rest.config.resources_converters') as $resource) {
            $definition->addMethodCall(
                'addConverter',
                [
                    $resource,
                    new Reference($resource)
                ]
            );
        }
    }
}

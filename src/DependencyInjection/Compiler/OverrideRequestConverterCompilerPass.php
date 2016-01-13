<?php

namespace Ibrows\RestBundle\DependencyInjection\Compiler;

use Ibrows\RestBundle\ParamConverter\RequestBodyParamConverter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class OverrideRequestConverterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('fos_rest.converter.request_body');
        if(!$definition) {
            return;
        }

        $arguments = $definition->getArguments();
        array_unshift($arguments, $container->getParameter('ibrows_rest.config.param_converter'));
        $definition->setArguments($arguments);

        $definition->setClass($container->getParameter('ibrows_rest.param_converter.request_body.class'));
    }
}

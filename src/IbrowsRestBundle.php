<?php

namespace Ibrows\RestBundle;

use Ibrows\RestBundle\DependencyInjection\Compiler\DebugViewResponseListenerCompilerPass;
use Ibrows\RestBundle\DependencyInjection\Compiler\OverrideRequestConverterCompilerPass;
use Ibrows\RestBundle\DependencyInjection\Compiler\ParamConvertersCompilerPass;
use Ibrows\RestBundle\DependencyInjection\Compiler\ResourceTransformerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @codeCoverageIgnore
 */
class IbrowsRestBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ParamConvertersCompilerPass());
        $container->addCompilerPass(new OverrideRequestConverterCompilerPass());
        $container->addCompilerPass(new DebugViewResponseListenerCompilerPass());
        $container->addCompilerPass(new ResourceTransformerCompilerPass());
    }
}

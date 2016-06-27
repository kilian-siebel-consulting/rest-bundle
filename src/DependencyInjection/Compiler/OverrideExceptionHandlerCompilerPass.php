<?php
namespace Ibrows\RestBundle\DependencyInjection\Compiler;

use Ibrows\RestBundle\Listener\ExceptionHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Class OverrideExceptionHandlerCompilerPass
 * @package Ibrows\RestBundle\DependencyInjection\Compiler
 *
 * @codeCoverageIgnore
 */
class OverrideExceptionHandlerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     * @throws ServiceNotFoundException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fos_rest.serializer.exception_normalizer.jms')) {
            return;
        }
        $container->getDefinition('fos_rest.serializer.exception_normalizer.jms')
            ->setClass(ExceptionHandler::class);
    }
}

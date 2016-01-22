<?php

namespace Ibrows\RestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 *
 * @codeCoverageIgnore
 */
class IbrowsRestExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('ibrows_rest.config.resources', $configuration['resources']);
        $container->setParameter('ibrows_rest.config.caches', $configuration['caches']);
        $container->setParameter('ibrows_rest.config.param_converter', $configuration['param_converter']);

        $fileLocator = new FileLocator( __DIR__ . '/../Resources/config');

        $loader = new XmlFileLoader($container, $fileLocator);
        $loader->load('collection_decorator.xml');
        $loader->load('debug_converter.xml');
        $loader->load('jms.xml');
        $loader->load('patch.xml');
        $loader->load('transformer.xml');
        $loader->load('utils.xml');

        if($configuration['param_converter']){
            $loader->load('param_converter.xml');
        }

        // Listeners are loaded dynamically according to the configuration.
        foreach ($configuration['listener'] as $name => $listener) {
            if ($listener['enabled']) {
                $loader->load('listener/' . $name . '.xml');
            }
            $container->setParameter('ibrows_rest.config.listener.' . $name, $listener);
        }

        foreach ($configuration['decorator'] as $name => $decorator) {
            $container->setParameter('ibrows_rest.config.decorator.' . $name, $decorator);
        }
    }
}

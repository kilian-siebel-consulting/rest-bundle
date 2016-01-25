<?php

namespace Ibrows\ExampleBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 *
 * @codeCoverageIgnore
 */
class IbrowsExampleExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configPath = __DIR__ . '/../Resources/config';

        $fileLocator = new FileLocator($configPath);
        $finder = new Finder();

        $loader = new XmlFileLoader($container, $fileLocator);
        /** @var SplFileInfo $xml */
        foreach ($finder->in($configPath)->name('*.xml') as $xml) {
            $loader->load($xml->getRelativePathname());
        }
    }
}

<?php
namespace Ibrows\RestBundle\Tests\Integration;

use Ibrows\RestBundle\Tests\app\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WebTestCase extends BaseWebTestCase
{
    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        if (static::$kernel === null || static::$kernel->getContainer() === null) {
            static::bootKernel();
        }
        return static::$kernel->getContainer();
    }

    /**
     * @param array $options
     * @return AppKernel
     */
    protected static function createKernel(array $options = array())
    {
        require_once __DIR__ . '/app/AppKernel.php';
        return new AppKernel(
            'config/config.yml',
            'test',
            true
        );
    }
}

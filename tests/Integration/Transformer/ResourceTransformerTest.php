<?php

namespace Ibrows\RestBundle\Tests\Integration\Transformer;

use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Tests\app\AppKernel;
use Ibrows\RestBundle\Transformer\Converter\ConverterInterface;
use Ibrows\RestBundle\Transformer\ResourceTransformer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class ResourceTransformerTest extends WebTestCase
{
    protected static function createKernel(array $options = array())
    {
        require_once __DIR__ . '/../app/AppKernel.php';
        return new AppKernel(
            'config/config.yml',
            'test',
            true
        );
    }

    /**
     * @return ResourceTransformer
     */
    private function createResourceTransformer()
    {
        if (!static::$kernel) {
            self::bootKernel();
        }

        self::bootKernel();
        /** @var RouterInterface $router */
        $router = static::$kernel->getContainer()->get('router');
        $inflector = static::$kernel->getContainer()->get('fos_rest.inflector.doctrine');
        return new ResourceTransformer($router, $inflector, [
            '/api/app_dev.php',
            '/api',
        ], 'test');
    }
    
    
    public function testFullResourceUrl()
    {
        $transformer = $this->createResourceTransformer();

        /** @var ConverterInterface|PHPUnit_Framework_MockObject_MockObject $converter */
        $converter = $this->getMockForAbstractClass(ConverterInterface::class);
        $converter
            ->expects($this->atLeastOnce())
            ->method('getResourceProxy')
            ->with(IntCategoryEntity::class, 1)
            ->willReturn('foo');

        $transformer->addConverter('testConverter', $converter);        

        $this->assertEquals('foo', $transformer->getResourceProxy('/api/v1/en_US/categories/1'));
        $this->assertEquals('foo', $transformer->getResourceProxy('/api/app_dev.php/v1/en_US/categories/1'));
    }

    public function failingUrlProvider()
    {
        return [
            ['https://somehost/app_dev.php/v1/en_US/categories/1'], 
            ['https://somehost/v1/en_US/categories/1'], 
            ['/v1/en_US/categories/1'], 
            ['/categories/1'], 
        ];
    }

    /**
     * @dataProvider failingUrlProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidResourceURLsShouldFail($url)
    {
        $transformer = $this->createResourceTransformer();

        $this->assertNull($transformer->getResourceProxy($url));
    }
}
class IntCategoryEntity implements ApiListableInterface
{
    public function getId()
    {
        return 42;
    }
}

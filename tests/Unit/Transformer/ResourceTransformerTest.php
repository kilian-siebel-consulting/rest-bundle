<?php
namespace Ibrows\RestBundle\Tests\Unit\Transformer;

use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Transformer\Converter\ConverterInterface;
use Ibrows\RestBundle\Transformer\ResourceTransformer;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class ResourceTransformerTest extends PHPUnit_Framework_TestCase
{
    public function testGetResourceProxy()
    {
        $config = [
            [
                'singular_name' => 'car',
                'plural_name' => 'cars',
                'converter' => 'test',
                'class' => CarEntity::class
            ],
            [
                'singular_name' => 'category',
                'plural_name' => 'categories',
                'converter' => 'test',
                'class' => CategoryEntity::class
            ]
        ];

        $transformer = new ResourceTransformer($config);

        /** @var ConverterInterface|PHPUnit_Framework_MockObject_MockObject $converter */
        $converter = $this->getMockForAbstractClass(ConverterInterface::class);
        $converter
            ->expects($this->once())
            ->method('getResourceProxy')
            ->with(CategoryEntity::class, 1)
            ->willReturn('foo');

        $transformer->addConverter('test', $converter);

        $this->assertEquals('foo', $transformer->getResourceProxy('/categories/1'));
    }

    public function testResourcePath()
    {
        $config = [
            [
                'singular_name' => 'car',
                'plural_name' => 'cars',
                'converter' => 'test',
                'class' => CarEntity::class
            ],
            [
                'singular_name' => 'category',
                'plural_name' => 'categories',
                'converter' => 'test',
                'class' => CategoryEntity::class
            ]
        ];

        $transformer = new ResourceTransformer($config);

        $this->assertTrue($transformer->isResourcePath('/categories/1'));
        $this->assertFalse($transformer->isResourcePath('categories/1'));
        $this->assertFalse($transformer->isResourcePath('/categories'));
        $this->assertFalse($transformer->isResourcePath(array('foo' => 'bar')));
    }

    public function testInvalidGetResourceProxy()
    {
        $transformer = new ResourceTransformer([]);

        $this->assertNull($transformer->getResourceProxy('/categories/1'));
    }

    public function testInvalidGetResource()
    {
        $transformer = new ResourceTransformer([]);

        $this->assertNull($transformer->getResource('/categories/1'));
    }

    public function testGetResource()
    {
        $config = [
            [
                'singular_name' => 'car',
                'plural_name' => 'cars',
                'converter' => 'test',
                'class' => CarEntity::class
            ],
            [
                'singular_name' => 'category',
                'plural_name' => 'categories',
                'converter' => 'test',
                'class' => CategoryEntity::class
            ]
        ];

        $transformer = new ResourceTransformer($config);

        /** @var ConverterInterface|PHPUnit_Framework_MockObject_MockObject $converter */
        $converter = $this->getMockForAbstractClass(ConverterInterface::class);
        $converter
            ->expects($this->once())
            ->method('getResource')
            ->with(CategoryEntity::class, 1)
            ->willReturn('foo');

        $transformer->addConverter('test', $converter);

        $this->assertEquals('foo', $transformer->getResource('/categories/1'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidPath()
    {
        $transformer = new ResourceTransformer([]);

        $transformer->getResourceProxy('/some/invalid/path');
    }

    public function testInvalidGetResourceConfig()
    {
        $transformer = new ResourceTransformer([]);

        $this->assertNull($transformer->getResourceConfig(new CategoryEntity()));
    }

    public function testGetResourceConfig()
    {
        $config = [
            [
                'singular_name' => 'car',
                'plural_name' => 'cars',
                'class' => CarEntity::class
            ],
            [
                'singular_name' => 'category',
                'plural_name' => 'categories',
                'class' => CategoryEntity::class
            ]
        ];

        $transformer = new ResourceTransformer($config);

        $this->assertEquals(
            [
                'singular_name' => 'category',
                'plural_name' => 'categories',
                'class' => CategoryEntity::class
            ],
            $transformer->getResourceConfig(new CategoryEntity())
        );
    }

    public function testInvalidGetResourcePath()
    {
        $transformer = new ResourceTransformer([]);

        $this->assertNull($transformer->getResourcePath(new CategoryEntity()));
    }

    public function testGetResourcePath()
    {
        $config = [
            [
                'singular_name' => 'car',
                'plural_name' => 'cars',
                'class' => CarEntity::class
            ],
            [
                'singular_name' => 'category',
                'plural_name' => 'categories',
                'class' => CategoryEntity::class
            ]
        ];

        $transformer = new ResourceTransformer($config);

        $this->assertEquals('/categories/42', $transformer->getResourcePath(new CategoryEntity()));
    }

    public function testInvalidIsResource()
    {
        $transformer = new ResourceTransformer([]);

        $this->assertFalse($transformer->isResource(CategoryEntity::class));
    }

    public function testIsResource()
    {
        $config = [
            [
                'singular_name' => 'car',
                'plural_name' => 'cars',
                'class' => CarEntity::class
            ],
            [
                'singular_name' => 'category',
                'plural_name' => 'categories',
                'class' => CategoryEntity::class
            ]
        ];

        $transformer = new ResourceTransformer($config);

        $this->assertTrue($transformer->isResource(CategoryEntity::class));
    }
}

class CarEntity implements ApiListableInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 7;
    }
}
class CategoryEntity implements ApiListableInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 42;
    }
}

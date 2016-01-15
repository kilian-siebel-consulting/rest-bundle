<?php
namespace Ibrows\RestBundle\Tests\Transformer;

use Doctrine\ORM\EntityManagerInterface;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Transformer\ResourceTransformer;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class ResourceTransformerTest extends PHPUnit_Framework_TestCase
{
    public function testGetResourceProxy()
    {
        /** @var EntityManagerInterface|PHPUnit_Framework_MockObject_MockObject $entityManager */
        $entityManager = $this->getMockForAbstractClass(EntityManagerInterface::class);

        $entityManager
            ->expects($this->once())
            ->method('getReference')
            ->with(CategoryEntity::class, 1)
            ->willReturn('foo');

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

        $transformer = new ResourceTransformer($entityManager, $config);

        $this->assertEquals('foo', $transformer->getResourceProxy('/categories/1'));
    }

    public function testGetResourcesName()
    {
        /** @var EntityManagerInterface|PHPUnit_Framework_MockObject_MockObject $entityManager */
        $entityManager = $this->getMockForAbstractClass(EntityManagerInterface::class);

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

        $transformer = new ResourceTransformer($entityManager, $config);

        $this->assertEquals('categories', $transformer->getResourcesName(new CategoryEntity()));
    }

    public function testGetResourceName()
    {
        /** @var EntityManagerInterface|PHPUnit_Framework_MockObject_MockObject $entityManager */
        $entityManager = $this->getMockForAbstractClass(EntityManagerInterface::class);

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

        $transformer = new ResourceTransformer($entityManager, $config);

        $this->assertEquals('category', $transformer->getResourceName(new CategoryEntity()));
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
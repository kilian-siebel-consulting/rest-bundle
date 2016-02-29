<?php
namespace Ibrows\RestBundle\Tests\Unit\Transformer;

use FOS\RestBundle\Util\Inflector\DoctrineInflector;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Transformer\Converter\ConverterInterface;
use Ibrows\RestBundle\Transformer\ResourceTransformer;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class ResourceTransformerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $config
     * @return ResourceTransformer
     */
    private function createResourceTransformer($routeConfig = []) 
    {
        $routerFaker = $this->getMockForAbstractClass(RouterInterface::class);

        if (count($routeConfig) > 0) {

            $routeCollection = new RouteCollection();
            
            foreach($routeConfig as $name => $config) {
                $options = [
                    ResourceTransformer::RESOURCE_ENTITY_ID_OPTION => 'id',
                    ResourceTransformer::RESOURCE_ENTITY_CLASS_OPTION => $config['class'],
                    ResourceTransformer::RESOURCE_CONVERTER_OPTION => isset($config['converter']) ? $config['converter'] : 'test',
                ];

                if (isset($config['singular_name'])) {
                    $options[ResourceTransformer::RESOURCE_SINGULAR_NAME] = $config['singular_name'];
                }

                if (isset($config['plural_name'])) {
                    $options[ResourceTransformer::RESOURCE_PLURAL_NAME] = $config['plural_name'];
                }
                
                $routeCollection->add($name, new Route($name, ['_route' => $name], [], $options));
            } 
                    
            $routerFaker
                ->method('match')
                ->willReturnCallback(function($url) use($routeConfig) {
                    foreach ($routeConfig as $routePattern => $class) {
                        if (preg_match($routePattern, $url, $matches)) {
                            return array_merge([ '_route' => $routePattern ], $matches); 
                        }
                    }
                    
                    throw new ResourceNotFoundException();
                });
            ;
            
            $routerFaker
                ->method('generate')
                ->willReturnCallback(function($route, $parameters) use($routeConfig, $routeCollection) {
                    if (!isset($routeConfig[$route])) {
                        throw new RouteNotFoundException();
                    }
                    
                    if (($routeObject = $routeCollection->get($route)) === null) {
                        throw new RouteNotFoundException();
                    }
                    
                    $config = $routeConfig[$route];
                    $route = '/api';

                    $idname = 'id';
                    if (isset($config['idname'])) {
                        $idname = $config['idname'];
                    }

                    if (!isset($config['generator']) && $routeObject->hasOption(ResourceTransformer::RESOURCE_PLURAL_NAME)) {
                        $generator = sprintf('/%s/%%d', $routeObject->getOption(ResourceTransformer::RESOURCE_PLURAL_NAME));
                    } else {
                        $generator = $config['generator']; 
                    }
                    
                    if (is_string($generator)) {
                        $id = $parameters[$idname];
                        return $route . sprintf($generator, $id);
                    } else {
                        return $route . $generator($parameters); 
                    }
                });

            $routerFaker
                ->method('getRouteCollection')
                ->willReturn($routeCollection)
            ;
        } else {
            $routerFaker
                ->expects($this->any())
                ->method('match')
                ->willThrowException(new ResourceNotFoundException())
            ;

            $routerFaker
                ->method('getRouteCollection')
                ->willReturn(new RouteCollection())
            ;
        }
        
        return new ResourceTransformer($routerFaker, new DoctrineInflector(), [
            '/api/app_dev.php',
            '/api'
        ]);
    }
    
    public function testGetResourceProxy()
    {
        $transformer = $this->createResourceTransformer([
            '@^/categories/(?P<id>\d+)$@' => [
                'class' => CategoryEntity::class,
                'converter' => 'test',
            ]
        ]);

        /** @var ConverterInterface|PHPUnit_Framework_MockObject_MockObject $converter */
        $converter = $this->getMockForAbstractClass(ConverterInterface::class);
        $converter
            ->expects($this->once())
            ->method('getResourceProxy')
            ->with(CategoryEntity::class, 1)
            ->willReturn('foo');

        $transformer->addConverter('test', $converter);

        $this->assertEquals('foo', $transformer->getResourceProxy('/api/categories/1'));
    }

    public function testResourcePath()
    {
        $transformer = $this->createResourceTransformer();

        $this->assertTrue($transformer->isResourcePath('/api/v1/en_US/categories/1'));
        $this->assertTrue($transformer->isResourcePath('/api/app_dev.php/v1/en_US/categories/1'));
        $this->assertFalse($transformer->isResourcePath('/categories/1'));
        $this->assertFalse($transformer->isResourcePath('categories/1'));
        $this->assertFalse($transformer->isResourcePath('/categories'));
        $this->assertFalse($transformer->isResourcePath(array('foo' => 'bar')));
    }

    public function testInvalidGetResourceProxy()
    {
        $transformer = $this->createResourceTransformer();

        $this->assertNull($transformer->getResourceProxy('/api/categories/1'));
    }

    public function testInvalidGetResource()
    {
        $transformer = $this->createResourceTransformer();

        $this->assertNull($transformer->getResource('/api/categories/1'));
    }

    public function testGetResource()
    {
        $transformer = $this->createResourceTransformer([
            '@^/categories/(?P<id>\d+)$@' => [
                'class' => CategoryEntity::class,
                'converter' => 'test',
                'singular_name' => 'category',
            ],
            '@^/car/(?P<id>\d+)$@' => [
                'class' => CarEntity::class,
                'converter' => 'test',
                'singular_name' => 'car',
            ],
        ]);

        /** @var ConverterInterface|PHPUnit_Framework_MockObject_MockObject $converter */
        $converter = $this->getMockForAbstractClass(ConverterInterface::class);
        $converter
            ->expects($this->once())
            ->method('getResource')
            ->with(CategoryEntity::class, 1)
            ->willReturn('foo');

        $transformer->addConverter('test', $converter);

        $this->assertEquals('foo', $transformer->getResource('/api/categories/1'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidPath()
    {
        $transformer = $this->createResourceTransformer();

        $transformer->getResourceProxy('/some/invalid/path');
    }

    public function testInvalidGetResourceConfig()
    {
        $transformer = $this->createResourceTransformer();

        $this->assertNull($transformer->getResourceConfig(new CategoryEntity()));
    }

    public function testGetResourceConfig()
    {
        $transformer = $this->createResourceTransformer([
            '@^/categories/(?P<id>\d+)$@' => [
                'class' => CategoryEntity::class,
                'converter' => 'test',
                'singular_name' => 'category',
                'generator' => '/categories/%d',
            ],
            '@^/cars/(?P<id>\d+)$@' => [
                'class' => CarEntity::class,
                'converter' => 'test',
                'singular_name' => 'car',
                'generator' => '/cars/%d',
            ],
        ]);

        $this->assertEquals(
            [
                'singular_name' => 'category',
                'plural_name' => 'categories',
                'class' => CategoryEntity::class,
                'route' => '@^/categories/(?P<id>\d+)$@',
                'converter' => 'test',
            ],
            $transformer->getResourceConfig(new CategoryEntity())
        );
    }

    public function testInvalidGetResourcePath()
    {
        $transformer = $this->createResourceTransformer();

        $this->assertNull($transformer->getResourcePath(new CategoryEntity()));
    }

    public function testGetResourcePath()
    {
        $transformer = $this->createResourceTransformer([
            '@^/categories/(?P<id>\d+)$@' => [
                'class' => CategoryEntity::class,
                'converter' => 'test',
                'singular_name' => 'category',
                'generator' => '/categories/%d',
            ],
            '@^/car/(?P<id>\d+)$@' => [
                'class' => CarEntity::class,
                'converter' => 'test',
                'singular_name' => 'car',
            ],
        ]);

        $this->assertEquals('/api/categories/42', $transformer->getResourcePath(new CategoryEntity()));
    }

    public function testInvalidIsResource()
    {
        $transformer = $this->createResourceTransformer();

        $this->assertFalse($transformer->isResource(CategoryEntity::class));
    }

    public function testIsResource()
    {
        $transformer = $this->createResourceTransformer([
            '@^/categories/(?P<id>\d+)$@' => [
                'class' => CategoryEntity::class,
                'converter' => 'test',
                'singular_name' => 'category',
            ],
            '@^/car/(?P<id>\d+)$@' => [
                'class' => CarEntity::class,
                'converter' => 'test',
                'singular_name' => 'car',
            ],
        ]);

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

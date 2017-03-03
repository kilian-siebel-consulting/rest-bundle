<?php
namespace Ibrows\RestBundle\Tests\Unit\ParamConverter;

require_once __DIR__ . '/LinkParamTestClasses.php';

use Ibrows\RestBundle\ParamConverter\ResourceUrlParamConverter;
use Ibrows\RestBundle\Transformer\TransformerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class ResourceUrlConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testSupportUsesResourceTransformer()
    {
        $transformer = self::createMock(TransformerInterface::class);

        $transformer
            ->expects($this->once())
            ->method('isResource')
            ->willReturn(true)
        ;

        $resourceTransformer = new ResourceUrlParamConverter($transformer);

        $configuration = new ParamConverter([
            'Name' => 'car',
            'Class' => Car::class
        ]);
        
        $this->assertTrue($resourceTransformer->supports($configuration));
    }
    
    public function testReturnValidResource()
    {
        $transformer = self::createMock(TransformerInterface::class);

        $userObject = new Car('23');
        
        $transformer
            ->expects($this->once())
            ->method('getResource')
            ->willReturn($userObject)
        ;
        
        $resourceTransformer = new ResourceUrlParamConverter($transformer);

        $request = new Request([
            'car' => '/v1/en_CH/cars/1'
        ]);

        $configuration = new ParamConverter([
            'Name' => 'car',
            'Class' => Car::class
        ]);
        
        $result = $resourceTransformer->apply($request, $configuration);

        $this->assertTrue($result);
        $this->assertTrue($request->attributes->has('car'));
        $this->assertEquals($userObject, $request->attributes->get('car'));
    }

    public function testWithMissingParameter()
    {
        $transformer = self::createMock(TransformerInterface::class);

        $transformer
            ->expects($this->never())
            ->method('getResource')
        ;

        $resourceTransformer = new ResourceUrlParamConverter($transformer);

        $request = new Request([
        ]);

        $configuration = new ParamConverter([
            'Name' => 'car',
            'Class' => Car::class
        ]);

        $result = $resourceTransformer->apply($request, $configuration);

        $this->assertFalse($result);
    }

    public function testWithInvalidUrlCausingException()
    {
        $transformer = self::createMock(TransformerInterface::class);

        $transformer
            ->expects($this->once())
            ->method('getResource')
            ->willThrowException(new \InvalidArgumentException("dummy"))
        ;

        $resourceTransformer = new ResourceUrlParamConverter($transformer);

        $request = new Request([
            'car' => 'false'
        ]);

        $configuration = new ParamConverter([
            'Name' => 'car',
            'Class' => Car::class
        ]);

        $result = $resourceTransformer->apply($request, $configuration);

        $this->assertFalse($result);
    }    
}

<?php
/**
 * Created by PhpStorm.
 * User: stefanvetsch
 * Date: 18.01.16
 * Time: 09:38
 */

namespace Ibrows\RestBundle\Tests\ParamConverter;

require_once __DIR__ . '/LinkParamTestClasses.php';

use Ibrows\RestBundle\ParamConverter\LinkParamConverter;
use Ibrows\RestBundle\Request\LinkHeader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LinkParamConverterTest extends \PHPUnit_Framework_TestCase
{
    private function createLinkHeader($definition, $resource)
    {
        $link = new LinkHeader($definition);
        $link->setResource($resource);
        
        return $link;
    }
    
    private function createLinkRequest($linkRequest)
    {
        $request = new Request([], [], [
            'links' => $linkRequest,
        ]);
        
        return $request;
    }
    
    private function getConfiguration(array $relations = ['wheels', 'doors'], $emptyRelation = false)
    {

        $configuration = $this
            ->getMockBuilder(ParamConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configArray = [
            'fail_on_validation_error' => true,
            'source' => 'car',
        ];
        
        if(!$emptyRelation) {
            $configArray['relations'] = $relations;
        }
        
        $configuration
            ->expects($this->atLeastOnce())
            ->method('getOptions')
            ->willReturn($configArray);

        $configuration
            ->expects($this->any())
            ->method('getName')
            ->willReturn('car');

        return $configuration;
    }
    
    public function testFullWorkingExample()
    {
        $car = new Car(1);
        
        $request = $this->createLinkRequest([
            $this->createLinkHeader('<wheel1>; rel="wheels";', new Wheel(1)),
            $this->createLinkHeader('<wheel2>; rel="wheels";', new Wheel(2)),
            $this->createLinkHeader('<wheel3>; rel="wheels";', new Wheel(3)),
            $this->createLinkHeader('<wheel4>; rel="wheels";', new Wheel(4)),
            $this->createLinkHeader('<door1>; rel="doors";', new Door(1)),
            $this->createLinkHeader('<door2>; rel="doors";', new Door(2)),
        ]);

        $configuration = $this->getConfiguration();
        
        $converter = $this->getConverter($car);
        $converter->apply($request, $configuration);
        
        $this->assertCount(4, $car->getWheels());
        $this->assertCount(2, $car->getDoors());
        
        $this->assertInstanceOf(Wheel::class,$car->getWheels()[0]);
        $this->assertInstanceOf(Door::class,$car->getDoors()[0]);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\ConflictHttpException
     */
    public function testDuplicateLink()
    {
        $car = new Car(1);
        $wheel = new Wheel(1);
        
        $request = $this->createLinkRequest([
            $this->createLinkHeader('<wheel1>; rel="wheels";', $wheel),
            $this->createLinkHeader('<wheel2>; rel="wheels";', $wheel),
        ]);

        $configuration = $this->getConfiguration();

        $converter = $this->getConverter($car);
        $converter->apply($request, $configuration);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testInvalidRelation()
    {
        $car = new Car(1);

        $request = $this->createLinkRequest([
            $this->createLinkHeader('<wheel1>; rel="wheels";', new Wheel(1)),
            $this->createLinkHeader('<wheel2>; rel="wheels";', new Wheel(2)),
        ]);

        $configuration = $this->getConfiguration(['doors']);
        
        $converter = $this->getConverter($car);
        $converter->apply($request, $configuration);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testNoRelationConfiguration()
    {
        $car = new Car(1);

        $request = $this->createLinkRequest([
            $this->createLinkHeader('<wheel1>; rel="wheels";', new Wheel(1)),
            $this->createLinkHeader('<wheel2>; rel="wheels";', new Wheel(2)),
        ]);

        $configuration = $this->getConfiguration(['wheels', 'doors'], true);

        $converter = $this->getConverter($car);
        $converter->apply($request, $configuration);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testNonExistingEntityRelation()
    {
        $car = new Car(1);

        $request = $this->createLinkRequest([
            new LinkHeader('<wheel1>; rel="wheels";')
        ]);

        $configuration = $this->getConfiguration(['wheels', 'doors']);

        $converter = $this->getConverter($car);
        $converter->apply($request, $configuration);
    }

    /**
     * @expectedException \Ibrows\RestBundle\Exception\BadRequestConstraintException
     */
    public function testWithFailingValidator()
    {
        $car = new Car(1);

        $request = $this->createLinkRequest([
            $this->createLinkHeader('<wheel1>; rel="wheels";', new Wheel(1)),
        ]);

        $configuration = $this->getConfiguration(['wheels', 'doors']);

        $converter = $this->getConverter($car, true);
        $converter->apply($request, $configuration);
    }
    
    public function testValidSupports()
    {
        $converter = $this->getConverter(null);

        $configuration = $this
            ->getMockBuilder(ParamConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configuration->method('getOptions')->willReturn(['source' => 'car']);

        $this->assertTrue($converter->supports($configuration));
    }
    
    public function testWithMissingSource()
    {
        $converter = $this->getConverter(null);

        $configuration = $this
            ->getMockBuilder(ParamConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configuration->method('getOptions')->willReturn([]);
        
        $this->assertFalse($converter->supports($configuration));
    }
    
    public function testWithMissingParamConverter()
    {
        $converter = $this->getConverter(null);

        $configuration = $this
            ->getMockBuilder(ParamConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configuration->method('getOptions')->willReturn(['source' => 'bla']);

        $this->assertFalse($converter->supports($configuration));
    }

    /**
     * @param      $car
     * @param bool $failingValidator
     * @return LinkParamConverter
     */
    public function getConverter($car, $failingValidator = false)
    {
        $validator = $this->getMockForAbstractClass(ValidatorInterface::class);
        
        $constraintViolationsListInterface = $this->getMockForAbstractClass(ConstraintViolationListInterface::class);

        $constraintViolationsListInterface
            ->method('count')
            ->willReturn((int)$failingValidator);


        $validator
            ->expects($this->any())
            ->method('validate')
            ->willReturn($constraintViolationsListInterface);
        
        
        $converter =  new LinkParamConverter([]);
        $converter->setValidator($validator);
        
        
        $carConverter = $this->getMockBuilder(ParamConverterInterface::class)
            ->getMock();


        $carConverter
            ->expects($this->any())
            ->method('supports')
            ->willReturn(true);
        
        $carConverter
            ->expects($this->any())
            ->method('apply')
            ->willReturnCallback(function(Request $request, ParamConverter $configuration) use($car) {
                $request->attributes->set('car', $car);
            });
        
        $converter->addConverter('car', $carConverter);
        
        return $converter;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: stefanvetsch
 * Date: 18.01.16
 * Time: 14:30
 */

namespace Ibrows\RestBundle\Tests\ParamConverter;

require_once __DIR__ . '/LinkParamTestClasses.php';

use Doctrine\Common\Collections\ArrayCollection;
use Ibrows\RestBundle\ParamConverter\LinkParamConverter;
use Ibrows\RestBundle\ParamConverter\UnlinkParamConverter;
use Ibrows\RestBundle\Request\LinkHeader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UnlinkParamConverterTest extends \PHPUnit_Framework_TestCase
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
    
    public function testUnlinkExisting()
    {
        $car = new Car(1);
        $wheel1 = new Wheel(1);
        $door1 = new Door(1);
        $car->setWheels(new ArrayCollection([$wheel1, new Wheel(2), new Wheel(3), new Wheel(4)]));
        $car->setDoors(new ArrayCollection([$door1, new Door(2)]));

        $request = $this->createLinkRequest([
            $this->createLinkHeader('<wheel1>; rel="wheels";', $wheel1),
            $this->createLinkHeader('<door1>; rel="doors";', $door1),
        ]);

        $configuration = $this->getConfiguration();

        $converter = $this->getConverter($car);
        $converter->apply($request, $configuration);

        $this->assertCount(3, $car->getWheels());
        $this->assertCount(1, $car->getDoors());
        
        $this->assertInstanceOf(Wheel::class,$car->getWheels()->first());
        $this->assertInstanceOf(Door::class,$car->getDoors()->first());
    }


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testUnlinkEntityNotFound()
    {
        $car = new Car(1);
        $wheel1 = new Wheel(1);
        $door1 = new Door(1);
        $car->setWheels(new ArrayCollection([$wheel1, new Wheel(2), new Wheel(3), new Wheel(4)]));
        $car->setDoors(new ArrayCollection([$door1, new Door(2)]));

        $request = $this->createLinkRequest([
            $this->createLinkHeader('<wheel1>; rel="wheels";', null),
        ]);

        $configuration = $this->getConfiguration();

        $converter = $this->getConverter($car);
        $converter->apply($request, $configuration);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\ConflictHttpException
     */
    public function testsUnlinkEntityNotInRelation()
    {
        $car = new Car(1);
        $wheel1 = new Wheel(1);
        $door1 = new Door(1);
        $car->setWheels(new ArrayCollection([$wheel1, new Wheel(2), new Wheel(3), new Wheel(4)]));
        $car->setDoors(new ArrayCollection([$door1, new Door(2)]));

        $request = $this->createLinkRequest([
            $this->createLinkHeader('<wheel1>; rel="wheels";', new Wheel(5)),
        ]);

        $configuration = $this->getConfiguration();

        $converter = $this->getConverter($car);
        $converter->apply($request, $configuration);
    }

    /**
     * @param Car $car
     * @return UnlinkParamConverter
     */
    public function getConverter($car)
    {
        $validator = $this->getMockForAbstractClass(ValidatorInterface::class);
        $converter =  new UnlinkParamConverter([
            'fail_on_validation_error' => true,
            'validation_errors_argument' => 'validationErrors',
        ], $validator);

        $carConverter = $this->getMockBuilder(ParamConverterInterface::class)
            ->getMock();

        $carConverter
            ->expects($this->any())
            ->method('supports')
            ->willReturn(true);

        $carConverter
            ->expects($this->any())
            ->method('apply')
            ->willReturnCallback(function(Request $request) use($car) {
                $request->attributes->set('car', $car);
            });

        $converter->addConverter('car', $carConverter);

        return $converter;
    }
}
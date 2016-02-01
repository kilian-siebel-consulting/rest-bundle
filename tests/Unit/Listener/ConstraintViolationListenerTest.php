<?php
namespace Ibrows\RestBundle\Tests\Unit\Listener;

use Ibrows\RestBundle\Listener\ConstraintViolationListener;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\JsonSerializationVisitor;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ConstraintViolationListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return mixed[][]
     */
    public function getData()
    {
        $data[] = array(
            array(
                'propertyPath' => 'a',
                'message'      => 'a',
                'code'         => 'a',
                'value'        => 'a',
            ),
            array(
                'propertyPath' => '/a',
                'message'      => 'a',
                'code'         => 'a',
                'value'        => 'a',
            )
        );
        $data[] = array(
            array(
                'propertyPath' => 'aa[bb]',
                'message'      => 'a',
                'code'         => 'a',
                'value'        => 'a',
            ),
            array(
                'propertyPath' => '/aa/bb',
                'message'      => 'a',
                'code'         => 'a',
                'value'        => 'a',
            )
        );
        $data[] = array(
            array(
                'propertyPath' => 'Aa[bb][cC][dddddddddd][eE]',
                'message'      => 'a',
                'code'         => 'a',
                'value'        => 'a',
            ),
            array(
                'propertyPath' => '/Aa/bb/cC/dddddddddd/eE',
                'message'      => 'a',
                'code'         => 'a',
                'value'        => 'a',
            )
        );
        $data[] = array(
            array(
                'propertyPath' => 'a',
                'message'      => 'aasdfkj77asdfhj((//{{',
                'code'         => 497845,
                'value'        => 'MyInvalidValue 99üüääjj@@//¥¥≤≤<<>>//||\\\\',
            ),
            array(
                'propertyPath' => '/a',
                'message'      => 'aasdfkj77asdfhj((//{{',
                'code'         => 497845,
                'value'        => 'MyInvalidValue 99üüääjj@@//¥¥≤≤<<>>//||\\\\',
            )
        );

        return $data;
    }

    /**
     * @dataProvider getData
     * @param array $data
     * @param array $expectedResult
     */
    public function testConstraintViolationListener(array $data, array $expectedResult)
    {
        $listener = $this->getListener();

        /** @var JsonSerializationVisitor|PHPUnit_Framework_MockObject_MockObject $visitor */
        $visitor = $this->getMock(JsonSerializationVisitor::class, array(), array(), '', false);

        /** @var ConstraintViolationInterface|PHPUnit_Framework_MockObject_MockObject $constraintViolation */
        $constraintViolation = $this->getMockForAbstractClass(ConstraintViolationInterface::class);
        $constraintViolation->method('getPropertyPath')->willReturn($data['propertyPath']);
        $constraintViolation->method('getMessage')->willReturn($data['message']);
        $constraintViolation->method('getCode')->willReturn($data['code']);
        $constraintViolation->method('getInvalidValue')->willReturn($data['value']);

        $result = $listener->serializeToJson($visitor, $constraintViolation);
        $this->assertArrayHasKey('propertyPath', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('value', $result);

        $this->assertEquals($expectedResult['propertyPath'], $result['propertyPath']);
        $this->assertEquals($expectedResult['message'], $result['message']);
        $this->assertEquals($expectedResult['code'], $result['code']);
        $this->assertEquals($expectedResult['value'], $result['value']);
    }

    public function testPreSerialize()
    {
        $listener = $this->getListener();

        $event = new PreSerializeEvent(
            DeserializationContext::create(),
            'something',
            ['some type']
        );

        $listener->onSerializerPreSerialize($event);

        $this->assertArraySubset([
            'name' => 'ibrows_test',
        ], $event->getType());
    }

    /**
     * @return ConstraintViolationListener
     */
    private function getListener()
    {
        return new ConstraintViolationListener('ibrows_test');
    }
}

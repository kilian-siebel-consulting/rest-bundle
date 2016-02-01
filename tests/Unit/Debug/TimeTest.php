<?php
namespace Ibrows\RestBundle\Tests\Unit\Debug;

use Ibrows\RestBundle\Debug\Converter\Time;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\HttpKernel\DataCollector\TimeDataCollector;

class TimeTest extends PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        /** @var TimeDataCollector|PHPUnit_Framework_MockObject_MockObject $collector */
        $collector = $this->getMock(TimeDataCollector::class);

        $collector->method('getStartTime')
            ->willReturn(microtime(true) * 1000);

        $this->assertRegExp(
            '/\d{1,2} ms/',
            (new Time())->convert($collector)
        );
    }

    /**
     * @param bool                   $expected
     * @param DataCollectorInterface $object
     * @dataProvider getSupportDataProvider
     */
    public function testSupports($expected, DataCollectorInterface $object)
    {
        $this->assertEquals($expected, (new Time())->supports($object));
    }

    /**
     * @return mixed[][]
     */
    public function getSupportDataProvider()
    {
        return [
            [false, new MemoryDataCollector()],
            [true, new TimeDataCollector()],
        ];
    }

    public function testGetName()
    {
        $this->assertEquals('time_elapsed', (new Time())->getName());
    }
}

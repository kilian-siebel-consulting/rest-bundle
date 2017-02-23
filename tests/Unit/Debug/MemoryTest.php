<?php
namespace Ibrows\RestBundle\Tests\Unit\Debug;

use Ibrows\RestBundle\Debug\Converter\Memory;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\HttpKernel\DataCollector\TimeDataCollector;

class MemoryTest extends PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        /** @var MemoryDataCollector|PHPUnit_Framework_MockObject_MockObject $collector */
        $collector = self::createMock(MemoryDataCollector::class);

        $collector->method('getMemory')
            ->willReturn(345135413547);

        $this->assertEquals(
            '321.43 GB',
            (new Memory())->convert($collector)
        );
    }

    /**
     * @param bool                   $expected
     * @param DataCollectorInterface $object
     * @dataProvider getSupportDataProvider
     */
    public function testSupports($expected, DataCollectorInterface $object)
    {
        $this->assertEquals($expected, (new Memory())->supports($object));
    }

    /**
     * @return mixed[][]
     */
    public function getSupportDataProvider()
    {
        return [
            [true, new MemoryDataCollector()],
            [false, new TimeDataCollector()],
        ];
    }

    public function testGetName()
    {
        $this->assertEquals('memory', (new Memory())->getName());
    }
}

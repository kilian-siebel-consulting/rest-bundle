<?php
namespace Ibrows\RestBundle\Tests\Unit\Debug;

use Doctrine\Bundle\DoctrineBundle\DataCollector\DoctrineDataCollector;
use Doctrine\Common\Persistence\ManagerRegistry;
use Ibrows\RestBundle\Debug\Converter\Db;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;

class DbTest extends PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        /** @var DoctrineDataCollector|PHPUnit_Framework_MockObject_MockObject $collector */
        $collector = self::createMock(DoctrineDataCollector::class);

        $collector->method('getMappingErrors')
            ->willReturn([
                'error'
            ]);

        $collector->method('getInvalidEntityCount')
            ->willReturn(7);

        $collector->method('getQueryCount')
            ->willReturn(42);

        $collector->method('getTime')
            ->willReturn(0.0431);

        $this->assertEquals(
            [
                'mappingErrors' => [
                    'error'
                ],
                'invalidEntities' => 7,
                'queryCount' => 42,
                'queryTime' => '0 ms',
            ],
            (new Db())->convert($collector)
        );
    }

    /**
     * @param bool                   $expected
     * @param DataCollectorInterface $object
     * @dataProvider getSupportDataProvider
     */
    public function testSupports($expected, DataCollectorInterface $object)
    {
        $this->assertEquals($expected, (new Db())->supports($object));
    }

    /**
     * @return mixed[][]
     */
    public function getSupportDataProvider()
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = self::createMock(ManagerRegistry::class);

        return [
            [true, new DoctrineDataCollector($managerRegistry)],
            [false, new MemoryDataCollector()],
        ];
    }

    public function testGetName()
    {
        $this->assertEquals('db', (new Db())->getName());
    }
}

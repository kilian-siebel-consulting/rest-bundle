<?php
namespace Ibrows\RestBundle\Tests\Unit\Debug;

use Ibrows\RestBundle\Debug\Converter\Security;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SecurityTest extends PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        /** @var SecurityDataCollector|PHPUnit_Framework_MockObject_MockObject $collector */
        $collector = $this->getMock(SecurityDataCollector::class);

        $collector->method('isEnabled')
            ->willReturn(true);

        $collector->method('isAuthenticated')
            ->willReturn(false);

        $collector->method('getUser')
            ->willReturn('foobar');

        $collector->method('getRoles')
            ->willReturn([
                'ROLE_BAR',
            ]);

        $collector->method('getTokenClass')
            ->willReturn(TokenInterface::class);

        $this->assertEquals(
            [
                'enabled' => true,
                'user' => 'foobar',
                'roles' => [
                    'ROLE_BAR',
                ],
                'authenticated' => false,
                'token' => TokenInterface::class
            ],
            (new Security())->convert($collector)
        );
    }

    /**
     * @param bool                   $expected
     * @param DataCollectorInterface $object
     * @dataProvider getSupportDataProvider
     */
    public function testSupports($expected, DataCollectorInterface $object)
    {
        $this->assertEquals($expected, (new Security())->supports($object));
    }

    /**
     * @return mixed[][]
     */
    public function getSupportDataProvider()
    {
        return [
            [false, new MemoryDataCollector()],
            [true, new SecurityDataCollector()],
        ];
    }

    public function testGetName()
    {
        $this->assertEquals('security', (new Security())->getName());
    }
}

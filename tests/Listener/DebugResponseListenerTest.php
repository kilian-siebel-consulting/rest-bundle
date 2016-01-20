<?php
namespace Ibrows\RestBundle\Tests\Listener;

use Ibrows\RestBundle\Debug\Converter\ConverterInterface;
use Ibrows\RestBundle\Listener\DebugResponseListener;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;
use SplObjectStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\DataCollector\TimeDataCollector;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\EventListener\ProfilerListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Profiler\Profile;

class DebugResponseListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ProfilerListener|PHPUnit_Framework_MockObject_MockObject
     */
    private $profileListener;

    /**
     * @var Profile[]
     */
    private $profiles;

    /**
     * @var HttpKernelInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $kernel;

    public function setUp()
    {
        $this->profileListener = $this->getMockBuilder(ProfilerListener::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->profiles = new SplObjectStorage();

        $profilesProperty = new ReflectionProperty($this->profileListener, 'profiles');
        $profilesProperty->setAccessible(true);
        $profilesProperty->setValue($this->profileListener, $this->profiles);
        $profilesProperty->setAccessible(false);


        $this->kernel = $this->getMockForAbstractClass(HttpKernelInterface::class);
    }

    /**
     * @param boolean $secure
     * @dataProvider getSecureInsecure
     */
    public function testOk($secure)
    {
        $listener = new DebugResponseListener(
            [
                'enabled'  => true,
                'key_name' => '_debug'
            ]
        );
        $listener->setProfilerListener($this->profileListener);
        $listener->addConverter(new FooConverter());

        $response = new Response(
            json_encode(
                [
                    'some' => [
                        'data',
                    ],
                ]
            ),
            200,
            [
                'Content-Type'       => 'application/json',
                'X-Debug-Token-Link' => '/token',
            ]
        );

        $request = new Request(
            [], [], [], [], [], [
                'SERVER_NAME' => 'foobar.test',
                'SERVER_PORT' => $secure ? 443 : 80,
                'HTTPS'       => $secure ? 'on' : 'off',
            ]
        );

        $profile = new Profile('token');
        $profile->addCollector(new TimeDataCollector());
        $profile->setIp('8.8.8.8');
        $profile->setMethod(Request::METHOD_HEAD);
        $profile->setUrl('/some/url');
        $profile->setStatusCode(Response::HTTP_I_AM_A_TEAPOT);
        $profile->setTime(1000000);

        $this->profiles[$request] = $profile;

        $listener->onKernelResponse($this->getEvent($request, $response));

        $debugResponse = json_decode($response->getContent(), true);

        $this->assertEquals(
            [
                'some'   => [
                    'data',
                ],
                '_debug' => [
                    'tokenUrl'   => $secure
                        ? 'https://foobar.test/token'
                        : 'http://foobar.test/token',
                    'ip'         => '8.8.8.8',
                    'method'     => Request::METHOD_HEAD,
                    'url'        => '/some/url',
                    'time'       => date('c', 1000000),
                    'statusCode' => Response::HTTP_I_AM_A_TEAPOT,
                    'foo'        => 'bar',
                ],
            ],
            $debugResponse
        );
    }

    public function testDisabled()
    {
        $listener = new DebugResponseListener(
            [
                'key_name' => '_debug'
            ]
        );
        $listener->setProfilerListener($this->profileListener);
        $listener->addConverter(new FooConverter());

        $response = new Response(
            json_encode(
                [
                    'some' => [
                        'data',
                    ],
                ]
            ),
            200,
            [
                'Content-Type' => 'application/json',
            ]
        );

        $request = new Request();

        $listener->onKernelResponse($this->getEvent($request, $response));

        $debugResponse = json_decode($response->getContent(), true);

        $this->assertEquals(
            [
                'some' => [
                    'data',
                ],
            ],
            $debugResponse
        );
    }

    public function testNoProfile()
    {
        $listener = new DebugResponseListener(
            [
                'enabled'  => true,
                'key_name' => '_debug'
            ]
        );
        $listener->setProfilerListener($this->profileListener);
        $listener->addConverter(new FooConverter());

        $response = new Response(
            json_encode(
                [
                    'some' => [
                        'data',
                    ],
                ]
            ),
            200,
            [
                'Content-Type' => 'application/json',
            ]
        );

        $request = new Request();

        $listener->onKernelResponse($this->getEvent($request, $response));

        $debugResponse = json_decode($response->getContent(), true);

        $this->assertEquals(
            [
                'some' => [
                    'data',
                ],
            ],
            $debugResponse
        );
    }

    /**
     * @return boolean[][]
     */
    public function getSecureInsecure()
    {
        return [
            [true,],
            [false,]
        ];
    }

    protected function getEvent(Request $request, Response $response)
    {
        return new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
    }
}

class FooConverter implements ConverterInterface
{

    /**
     * @param DataCollectorInterface $dataCollector
     * @return mixed|null
     */
    public function convert(DataCollectorInterface $dataCollector)
    {
        return 'bar';
    }

    /**
     * @param DataCollectorInterface $dataCollector
     * @return boolean
     */
    public function supports(DataCollectorInterface $dataCollector)
    {
        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'foo';
    }
}
<?php
namespace Ibrows\RestBundle\Tests\ParamConverter;

use Ibrows\RestBundle\ParamConverter\PatchParamConverter;
use Ibrows\RestBundle\Patch\Executioner;
use Ibrows\RestBundle\Patch\Operation\Change;
use JMS\Serializer\SerializerInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class PatchParamConverterTest extends PHPUnit_Framework_TestCase
{
    public function testValid()
    {
        $subject = new TestClass();
        $patch = [
            new Change(),
        ];

        /** @var SerializerInterface|PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->getMockForAbstractClass(SerializerInterface::class);

        /** @var Executioner|PHPUnit_Framework_MockObject_MockObject $executioner */
        $executioner = $this->getMockBuilder(Executioner::class)
            ->disableOriginalConstructor()
            ->getMock();

        $executioner
            ->expects($this->once())
            ->method('execute')
            ->with($subject, $patch)
            ->will($this->returnCallback(function(TestClass $subject, array $patch) {
                $subject->setPatched();
            }));

        /** @var ParamConverterInterface|PHPUnit_Framework_MockObject_MockObject $sourceConverter */
        $sourceConverter = $this->getMockForAbstractClass(ParamConverterInterface::class);

        /** @var ParamConverterInterface|PHPUnit_Framework_MockObject_MockObject $bodyConverter */
        $bodyConverter = $this->getMockForAbstractClass(ParamConverterInterface::class);

        /** @var ParamConverter|PHPUnit_Framework_MockObject_MockObject $configuration */
        $configuration = $this->getMockBuilder(ParamConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configuration
            ->method('getOptions')
            ->willReturn([
                'source' => 'source'
            ]);

        $configuration
            ->method('getName')
            ->willReturn('foo');

        $converter = new PatchParamConverter([], $serializer, $executioner);
        $converter->addConverter('source', $sourceConverter);
        $converter->addConverter('fos_rest.request_body', $bodyConverter);

        $request = new Request(
            [],
            [],
            [
                'operations' => $patch,
                'foo' => $subject,
            ]
        );

        $converter->apply($request, $configuration);

        $this->assertTrue($request->get('foo')->isPatched());
    }

    /**
     * @expectedException \Ibrows\RestBundle\Exception\BadRequestConstraintException
     */
    public function testInvalid()
    {
        $subject = new TestClass();
        $patch = [
            new Change(),
        ];

        /** @var SerializerInterface|PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->getMockForAbstractClass(SerializerInterface::class);

        /** @var Executioner|PHPUnit_Framework_MockObject_MockObject $executioner */
        $executioner = $this->getMockBuilder(Executioner::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var ParamConverterInterface|PHPUnit_Framework_MockObject_MockObject $sourceConverter */
        $sourceConverter = $this->getMockForAbstractClass(ParamConverterInterface::class);

        /** @var ParamConverterInterface|PHPUnit_Framework_MockObject_MockObject $bodyConverter */
        $bodyConverter = $this->getMockForAbstractClass(ParamConverterInterface::class);

        /** @var ConstraintViolationListInterface|PHPUnit_Framework_MockObject_MockObject $validationErrors */
        $validationErrors = $this->getMockForAbstractClass(ConstraintViolationListInterface::class);

        $validationErrors
            ->method('count')
            ->willReturn(2);

        /** @var ParamConverter|PHPUnit_Framework_MockObject_MockObject $configuration */
        $configuration = $this->getMockBuilder(ParamConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configuration
            ->method('getOptions')
            ->willReturn([
                'source' => 'source'
            ]);

        $configuration
            ->method('getName')
            ->willReturn('foo');

        $converter = new PatchParamConverter([], $serializer, $executioner);
        $converter->addConverter('source', $sourceConverter);
        $converter->addConverter('fos_rest.request_body', $bodyConverter);

        $request = new Request(
            [],
            [],
            [
                'operations' => $patch,
                'foo' => $subject,
                'validationErrors' => $validationErrors,
            ]
        );

        $converter->apply($request, $configuration);
    }
}

class TestClass
{
    /**
     * @return boolean
     */
    protected $patched = false;

    /**
     * @return boolean
     */
    public function isPatched()
    {
        return $this->patched;
    }

    public function setPatched()
    {
        $this->patched = true;
    }
}
<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Operation as Operation;
use JMS\Serializer\Metadata\PropertyMetadata;
use Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Intl\Exception\NotImplementedException;

class Executioner
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * Executioner constructor.
     *
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function execute($object, OperationCollection $patch)
    {
        $metadata = $this->getMetadata($object);
        foreach($patch as $operation) {
            $property = $this->getProperty($metadata, $operation->getPath());
            if(!$property) {
                throw new BadRequestHttpException('Property ' . $operation->getPath() . ' does not exist or is not writable.');
            }

            switch(get_class($operation)) {
                case Operation\Change::class:
                    $this->setProperty($property, $object, $operation->getValue());
                    break;
                case Operation\Clear::class:
                    $this->setProperty($property, $object, null);
                    break;
                default:
                    throw new NotImplementedException('Operation ' . get_class($operation) . ' is not implemented.');
            }
        }
    }

    /**
     * @param ClassMetadata $metadata
     * @param               $propertyPath
     *
     * @return PropertyMetadata
     */
    public function getProperty(ClassMetadata $metadata, $propertyPath)
    {
        $propertyPath = substr($propertyPath, 1);

        $properties = array_filter(
            $metadata->propertyMetadata,
            function(PropertyMetadata $propertyMetadata) use ($propertyPath) {
                $name = $propertyMetadata->serializedName !== null ? $propertyMetadata->serializedName : $propertyMetadata->name;
                return $name === $propertyPath &&
                    $propertyMetadata->readOnly === false;
            }
        );
        return array_shift($properties);
    }

    /**
     * @param $object
     *
     * @return ClassMetadata
     */
    private function getMetadata($object)
    {
        $class = new \ReflectionClass($object);

        return $this->driver->loadMetadataForClass($class);
    }

    /**
     * @param PropertyMetadata $property
     * @param mixed            $object
     * @param mixed            $value
     */
    private function setProperty(PropertyMetadata $property, $object, $value)
    {
        $property->setValue($object, $value);
    }
}
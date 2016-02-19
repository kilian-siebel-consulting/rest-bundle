<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Exception\NotImplementedException;
use Ibrows\RestBundle\Patch\Operation as Operation;
use JMS\Serializer\Metadata\PropertyMetadata;
use Metadata\ClassMetadata;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Executioner
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * Executioner constructor.
     *
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @param object               $object
     * @param OperationInterface[] $patch
     * @throws NotImplementedException
     */
    public function execute($object, array $patch)
    {
        array_walk(
            $patch,
            function (OperationInterface $operation) use ($object) {
                $property = $this->getProperty($object, $operation->getPath());
                if (!$property) {
                    throw new BadRequestHttpException(
                        sprintf(
                            'Property %s does not exist or is not writable.',
                            $operation->getPath()
                        )
                    );
                }
                $operation->apply($object, $property);
            }
        );
    }

    /**
     * @param object $object
     * @param string $propertyPath
     *
     * @return PropertyMetadata
     */
    private function getProperty($object, $propertyPath)
    {
        $metadata = $this->getMetadata($object);

        $propertyPath = substr($propertyPath, 1);

        $properties = array_filter(
            $metadata->propertyMetadata,
            function (PropertyMetadata $propertyMetadata) use ($propertyPath) {
                $name = $propertyMetadata->serializedName !== null
                    ? $propertyMetadata->serializedName
                    : $propertyMetadata->name;
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
        return $this->metadataFactory->getMetadataForClass(get_class($object)); 
    }
}

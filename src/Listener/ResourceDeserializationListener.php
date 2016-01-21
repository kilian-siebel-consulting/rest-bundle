<?php
namespace Ibrows\RestBundle\Listener;

use Ibrows\RestBundle\Transformer\TransformerInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use Metadata\MetadataFactoryInterface;
use RecursiveArrayIterator;

class ResourceDeserializationListener implements EventSubscriberInterface
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * EntityDeserializationListener constructor.
     * @param MetadataFactoryInterface $metadataFactory
     * @param TransformerInterface     $transformer
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        TransformerInterface $transformer
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event'  => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize'
            ]
        ];
    }

    /**
     * @param PreDeserializeEvent $event
     */
    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $data = $event->getData();
        $iterator = new RecursiveArrayIterator($data, 1);

        iterator_apply(
            $iterator,
            [$this, 'iterate'],
            [$iterator, $event->getType()]
        );

        $event->setData($iterator->getArrayCopy());
    }

    /**
     * This function walks over all items of $iterator.
     * At the same time the ClassMetadata of $type gets followed.
     *
     * If a property is a resource, the resourceProxy will be put there instead.
     *
     * @param RecursiveArrayIterator $iterator
     * @param array                  $type
     */
    protected function iterate(RecursiveArrayIterator $iterator, array $type)
    {
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->metadataFactory->getMetadataForClass($type['name']);

        while ($iterator->valid()) {
            $property = $this->getProperty($classMetadata, $iterator->key());

            if (
                $property &&
                $property->type['name'] === 'array' &&
                count($property->type['params']) > 0 &&
                $iterator->hasChildren()
            ) {
                $children = $iterator->getChildren();

                iterator_apply(
                    $children,
                    [$this, 'iterateArray'],
                    [$children, $property->type['params'][0]]
                );

                $iterator->offsetSet($iterator->key(), $children->getArrayCopy());
            } elseif (
                $property &&
                $iterator->hasChildren()
            ) {
                iterator_apply(
                    $iterator->getChildren(),
                    [$this, 'iterate'],
                    [$iterator->getChildren(), $property->type]
                );
            } elseif (
                $property &&
                is_string($iterator->current()) &&
                $this->transformer->isResource($property->type['name'])
            ) {
                $this->tryToTransform($iterator);
            }

            $iterator->next();
        }
    }

    /**
     * @param RecursiveArrayIterator $iterator
     * @param array                  $type
     */
    protected function iterateArray(RecursiveArrayIterator $iterator, array $type)
    {
        while ($iterator->valid()) {
            if($iterator->hasChildren()) {
                iterator_apply(
                    $iterator->getChildren(),
                    [$this, 'iterate'],
                    [$iterator->getChildren(), $type]
                );
            } elseif(
                is_string($iterator->current()) &&
                $this->transformer->isResource($type['name'])
            ) {
                $this->tryToTransform($iterator);
            }

            $iterator->next();
        }
    }

    /**
     * @param RecursiveArrayIterator $iterator
     */
    protected function tryToTransform(RecursiveArrayIterator $iterator)
    {
        try {
            $resource = $this->transformer->getResourceProxy($iterator->current());
            if($resource) {
                $iterator->offsetSet($iterator->key(), $resource);
            }
        } catch (\InvalidArgumentException $e) {
            // Didn't work, do nothing
        }
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param string        $propertyName
     * @return PropertyMetadata
     */
    protected function getProperty(ClassMetadata $classMetadata, $propertyName)
    {
        /** @var ClassMetadata[] $matching */
        $matching = array_filter(
            $classMetadata->propertyMetadata,
            function (PropertyMetadata $property) use ($propertyName) {
                return $property->name === $propertyName ||
                $property->serializedName === $propertyName;
            }
        );
        return array_shift($matching);
    }
}
<?php
namespace Ibrows\RestBundle\Listener;

use Ibrows\RestBundle\Transformer\TransformerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\VisitorInterface;

class ResourceDeserializationListener
{

    private $originalTypeParamName = 'originalType';

    /**
     * @var string
     */
    private $typeNameStrict;

    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * @param TransformerInterface $transformer
     * @param string               $typeNameStrict
     */
    public function __construct(TransformerInterface $transformer, $typeNameStrict)
    {
        $this->typeNameStrict = $typeNameStrict;
        $this->transformer = $transformer;
    }

    /**
     * @param VisitorInterface $visitor
     * @param                  $data
     * @param array            $type
     * @param Context          $context
     * @return \Ibrows\RestBundle\Model\ApiListableInterface|null
     */
    public function deserializeStrict(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $className = $this->getValidOriginalClassname($type);

        $resource = $this->transformer->getResourceProxy($data);
        if (is_object($resource) && $resource instanceof $className) {
            return $resource;
        }

        return null;

    }

    /**
     * @param array $type
     * @return string className
     */
    private function getValidOriginalClassname(array $type)
    {
        if (!isset($type['params'][$this->originalTypeParamName])) {
            throw new \RuntimeException(
                sprintf(
                    'The parameter %s has to be defined for %s.',
                    $this->originalTypeParamName,
                    $this->typeNameStrict
                )
            );
        }

        $class = $type['params'][$this->originalTypeParamName];

        if (!class_exists($class) && !interface_exists($class)) {
            throw new \RuntimeException(
                sprintf(
                    'The class or interface %s does not exist.',
                    $class
                )
            );
        }

        return $class;
    }

    /**
     * @param PreDeserializeEvent $event
     */
    public function onSerializerPreDeserialize(PreDeserializeEvent $event)
    {

        if ($this->transformer->isResourcePath($event->getData()) &&
            $this->transformer->isResource($event->getType()['name'])
        ) {
            $event->setType($this->typeNameStrict, [$this->originalTypeParamName => $event->getType()['name']]);
            return;
        }

        //  @JMS\Type("array<CLASSNAME>")
        if (is_array($event->getData()) &&
            $this->containsResources($event->getData()) &&
            isset($event->getType()['params'][0]['name']) &&
            $this->transformer->isResource($event->getType()['params'][0]['name'])
        ) {
            $event->setType($event->getType()['name'], [['name' => $this->typeNameStrict, 'params' => [$this->originalTypeParamName => $event->getType()['params'][0]['name']]]]);
        }

    }

    /**
     * @param array $array
     * @return boolean
     */
    public function containsResources(array $array)
    {
        $filtered = array_filter(
            $array,
            function ($var) {
                return !$this->transformer->isResourcePath($var);
            }
        );

        return count($filtered) === 0;
    }
}

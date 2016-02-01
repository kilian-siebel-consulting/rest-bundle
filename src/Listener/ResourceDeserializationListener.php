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
        try {
            $resource = $this->transformer->getResourceProxy($data);
            if (is_object($resource) && $resource instanceof $className) {
                return $resource;
            }
        } catch (\InvalidArgumentException $e) {
            // Data may be invalid, nothing should happen
        }
        return null;

    }

    /**
     * @param array $type
     * @return null|string className
     */
    private function getValidOriginalClassname(array $type)
    {
        if (!isset($type['params'][$this->originalTypeParamName])) {
            return null;
        }
        $class = $type['params'][$this->originalTypeParamName];
        if (class_exists($class) || interface_exists($class)) {
            return $class;
        }

        return null;
    }

    /**
     * @param PreDeserializeEvent $event
     */
    public function onSerializerPreDeserialize(PreDeserializeEvent $event)
    {

        if ($this->transformer->isResourcePath($event->getData())
            && $this->transformer->isResource($event->getType()['name'])) {
            $event->setType($this->typeNameStrict, [$this->originalTypeParamName => $event->getType()['name']]);
            return;
        }

        //  @JMS\Type("array<CLASSNAME>")
        if ($this->arrayContainsResources($event->getData())
            && isset($event->getType()['params'][0]['name'])
            && $this->transformer->isResource($event->getType()['params'][0]['name'])) {
            $event->setType($event->getType()['name'], [['name' => $this->typeNameStrict, 'params' => [$this->originalTypeParamName => $event->getType()['params'][0]['name']]]]);
        }

    }

    /**
     * @param mixed $data
     * @return boolean
     */
    public function arrayContainsResources($array)
    {
        if(is_array($array)){
            $filtered = array_filter($array, function( $var ){
                return $this->transformer->isResourcePath($var);
            });

            if(count($filtered) > 0){
                return true;
            }

            return false;
        }

        return false;
    }
}

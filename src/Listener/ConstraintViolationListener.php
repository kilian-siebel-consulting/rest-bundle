<?php
namespace Ibrows\RestBundle\Listener;

use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Validator\ConstraintViolation;

class ConstraintViolationListener
{
    /**
     * @var string
     */
    private $typeName;

    /**
     * @param string $typeName
     */
    public function __construct($typeName)
    {
        $this->typeName = $typeName;
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param ConstraintViolation      $violation
     * @return array
     */
    public function serializeToJson(JsonSerializationVisitor $visitor, ConstraintViolation $violation)
    {
        $data = array(
            'propertyPath' => $this->transformPropertyPath($violation->getPropertyPath()),
            'message'      => $violation->getMessage(),
            'code'         => $violation->getCode(),
            'value'        => $violation->getInvalidValue(),
        );

        if (null === $visitor->getRoot()) {
            $visitor->setRoot($data);
        }

        return $data;
    }

    /**
     * @param $path
     * @return string
     */
    private function transformPropertyPath($path){
        $path = new PropertyPath($path);
        return '/' . implode('/', $path->getElements());
    }


    /**
     * @param PreSerializeEvent $event
     */
    public function onSerializerPreSerialize(PreSerializeEvent $event)
    {
        $event->setType($this->typeName);
    }
}

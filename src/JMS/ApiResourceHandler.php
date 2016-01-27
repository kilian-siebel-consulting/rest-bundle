<?php
namespace Ibrows\RestBundle\JMS;

use Ibrows\RestBundle\Listener\ResourceDeserializationListener;
use Ibrows\RestBundle\Transformer\TransformerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;

class ApiResourceHandler implements SubscribingHandlerInterface
{
    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * ApiResourceHandler constructor.
     * @param TransformerInterface $transformer
     */
    public function __construct(
        TransformerInterface $transformer
    ) {
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format'    => 'json',
                'type'      => ResourceDeserializationListener::TYPE_NAME,
                'method'    => 'deserialize',
            ],
        ];
    }

    /**
     * @param VisitorInterface $visitor
     * @param string           $path
     * @param array            $type
     * @param Context          $context
     * @return mixed
     */
    public function deserialize(VisitorInterface $visitor, $path, array $type, Context $context)
    {
        $className = $this->getValidOriginalClassname($type);

        try {
            $resource = $this->transformer->getResourceProxy($path);

            if (is_object($resource) && $className && !$resource instanceof $className) {
                return null;
            } elseif (is_object($resource) && (!$className || $resource instanceof $className)) {
                return $resource;
            }
        } catch (\InvalidArgumentException $e) {
            // Data may be invalid, nothing should happen
        }

        return $path;
    }
    
    private function getValidOriginalClassname($type)
    {
        if (!isset($type['params']['originalType'])) {
            return null;
        }

        if (class_exists($type['params']['originalType'])
            || interface_exists($type['params']['originalType'])
        ) {
            return $type['params']['originalType'];
        }

        return null;
    }
}

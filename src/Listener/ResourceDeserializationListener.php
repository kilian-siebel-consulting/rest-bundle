<?php
namespace Ibrows\RestBundle\Listener;

use Ibrows\RestBundle\Transformer\TransformerInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;

class ResourceDeserializationListener implements EventSubscriberInterface
{
    const TYPE_NAME = 'ibrows_rest_resource';

    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * EntityDeserializationListener constructor.
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
    public static function getSubscribedEvents()
    {
        return [
            [
                'event'  => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize'
            ],
        ];
    }

    /**
     * @param PreDeserializeEvent $event
     */
    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        if (
            is_string($event->getData()) &&
            $this->transformer->isResource($event->getType()['name'])
        ) {
            $event->setType(self::TYPE_NAME);
        } elseif (
            is_string($event->getData()) &&
            isset($event->getType()['params'][0]['name']) &&
            $this->transformer->isResource($event->getType()['params'][0]['name'])
        ) {
            $event->setType(
                'array',
                [
                    [
                        'name' => self::TYPE_NAME,
                    ],
                ]
            );
        }
    }
}
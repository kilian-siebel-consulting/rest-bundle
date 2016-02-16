<?php
namespace Ibrows\RestBundle\Patch;

use JMS\Serializer\SerializerInterface;

class JMSValueConverter implements ValueConverterInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * JMSValueConverter constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($value, ValueInterface $pathValue)
    {
        if ($pathValue->type() === null) {
            return $value;
        }
        $jsonValue = json_encode($value);

        return $this->serializer->deserialize($jsonValue, $pathValue->type()['name'], 'json');
    }
}

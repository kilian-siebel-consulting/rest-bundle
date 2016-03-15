<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Exception\InvalidValueException;
use InvalidArgumentException;
use JMS\Serializer\Exception\Exception;
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

        try {
            return $this->serializer->deserialize($jsonValue, $pathValue->type()['name'], 'json');
        }catch(Exception $e){
            throw new InvalidValueException($e->getMessage(), $e);
        }

    }
}

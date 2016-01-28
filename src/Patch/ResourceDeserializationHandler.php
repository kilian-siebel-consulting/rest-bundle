<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Transformer\TransformerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\VisitorInterface;

class ResourceDeserializationHandler
{
    /**
     * @var string
     */
    private $typeName;

    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * @param TransformerInterface $transformer
     * @param                      $typeName
     */
    public function __construct(TransformerInterface $transformer, $typeName)
    {
        $this->typeName = $typeName;
        $this->transformer = $transformer;
    }


    /**
     * @param VisitorInterface $visitor
     * @param                  $data
     * @param array            $type
     * @param Context          $context
     * @return \Ibrows\RestBundle\Model\ApiListableInterface|null
     */
    public function deserializeWeak(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        try {
            $resource = $this->transformer->getResourceProxy($data);
            if (is_object($resource)) {
                return $resource;
            }
        } catch (\InvalidArgumentException $e) {
            // Data may be invalid, nothing should happen
        }
        return $data;
    }
}

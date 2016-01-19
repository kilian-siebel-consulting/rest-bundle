<?php
namespace Ibrows\RestBundle\CollectionDecorator;

use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Representation\OffsetRepresentation;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\ParameterBag;

class OffsetDecorator implements DecoratorInterface
{
    /**
     * @var string
     */
    private $offsetParameterName;

    /**
     * @var string
     */
    private $limitParameterName;

    public function __construct(
        array $configuration
    ) {
        $this->offsetParameterName = $configuration['offset_parameter_name'];
        $this->limitParameterName = $configuration['limit_parameter_name'];
    }

    /**
     * {@inheritdoc}
     */
    public function decorate(ParameterBag $params, $collection)
    {
        if (
            !$collection instanceof CollectionRepresentation ||
            !$params->has('paramFetcher') ||
            !$params->has('_route')
        ) {
            return $collection;
        }

        try {
            return new OffsetRepresentation(
                $collection,
                $params->get('_route'),
                $params->all(),
                $params->get('paramFetcher')->get($this->offsetParameterName),
                $params->get('paramFetcher')->get($this->limitParameterName),
                null,
                $this->offsetParameterName,
                $this->limitParameterName
            );
        } catch (InvalidArgumentException $exception) {
            return $collection;
        }
    }
}
<?php
namespace Ibrows\RestBundle\CollectionDecorator;

use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Representation\LastIdRepresentation;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\ParameterBag;

class LastIdDecorator implements DecoratorInterface
{
    /**
     * @var string
     */
    private $sortByParameterName;

    /**
     * @var string
     */
    private $sortDirectionParameterName;

    /**
     * @var string
     */
    private $offsetIdParameterName;

    /**
     * @var string
     */
    private $limitParameterName;

    public function __construct(
        array $configuration
    ) {
        $this->sortByParameterName = $configuration['sort_by_parameter_name'];
        $this->sortDirectionParameterName = $configuration['sort_direction_parameter_name'];
        $this->offsetIdParameterName = $configuration['offset_id_parameter_name'];
        $this->limitParameterName = $configuration['limit_parameter_name'];
    }

    /**
     * {@inheritdoc}
     */
    public function decorate(ParameterBag $params, $collection)
    {
        if (!$collection instanceof CollectionRepresentation ||
            !$params->has('paramFetcher') ||
            !$params->has('_route')
        ) {
            return $collection;
        }

        try {
            $resources = $collection->getResources();
            $lastElement = end($resources);

            return new LastIdRepresentation(
                $collection,
                $params->get('_route'),
                $params->all(),
                $lastElement->getId(),
                $this->offsetIdParameterName,
                $params->get('paramFetcher')->get($this->limitParameterName),
                $this->limitParameterName,
                $params->get('paramFetcher')->get($this->sortByParameterName),
                $this->sortByParameterName,
                $params->get('paramFetcher')->get($this->sortDirectionParameterName),
                $this->sortDirectionParameterName
            );
        } catch (InvalidArgumentException $exception) {
            return $collection;
        }
    }
}

<?php
namespace Ibrows\RestBundle\CollectionDecorator;

use FOS\RestBundle\Request\ParamFetcherInterface;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\OffsetRepresentation;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\ParameterBag;

class OffsetDecorator extends AbstractDecorator
{
    /**
     * @var string
     */
    private $offsetParameterName;

    /**
     * @var string
     */
    private $limitParameterName;

    /**
     * @var ParamFetcherInterface
     */
    protected $paramFetcher;

    public function __construct(
        array $configuration,
        ParamFetcherInterface $paramFetcher
    ) {
        $this->offsetParameterName = $configuration['offset_parameter_name'];
        $this->limitParameterName = $configuration['limit_parameter_name'];
        $this->paramFetcher = $paramFetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function decorate(ParameterBag $params, $collection)
    {
        if (!$collection instanceof CollectionRepresentation ||
            !$params->has('_route')
        ) {
            return $collection;
        }

        try {
            return new OffsetRepresentation(
                $collection,
                $params->get('_route'),
                $this->getRouteParameters($params, $this->paramFetcher),
                $this->paramFetcher->get($this->offsetParameterName),
                $this->paramFetcher->get($this->limitParameterName),
                null,
                $this->offsetParameterName,
                $this->limitParameterName
            );
        } catch (InvalidArgumentException $exception) {
            return $collection;
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getInternalParameters()
    {
        return [
            $this->limitParameterName,
            $this->offsetParameterName,
        ];
    }
}

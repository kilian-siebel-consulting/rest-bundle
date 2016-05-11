<?php
namespace Ibrows\RestBundle\CollectionDecorator;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
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
                $params->all(),
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
}

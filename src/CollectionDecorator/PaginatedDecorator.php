<?php
namespace Ibrows\RestBundle\CollectionDecorator;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Representation\PaginationRepresentation;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\ParameterBag;

class PaginatedDecorator implements DecoratorInterface
{
    /**
     * @var string
     */
    private $pageParameterName;

    /**
     * @var string
     */
    private $limitParameterName;

    /**
     * @var ParamFetcherInterface
     */
    private $paramFetcher;

    public function __construct(
        array $configuration,
        ParamFetcherInterface $paramFetcher
    ) {
        $this->pageParameterName = $configuration['page_parameter_name'];
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
            if ($this->paramFetcher->get($this->limitParameterName) === null ||
                $this->paramFetcher->get($this->pageParameterName) === null
            ) {
                return $collection;
            }

            return new PaginationRepresentation(
                $collection,
                $params->get('_route'),
                $params->all(),
                $this->paramFetcher->get($this->pageParameterName),
                $this->paramFetcher->get($this->limitParameterName),
                null,
                $this->pageParameterName,
                $this->limitParameterName,
                false
            );
        } catch (InvalidArgumentException $exception) {
            return $collection;
        }
    }
}

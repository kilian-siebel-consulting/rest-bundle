<?php
namespace Ibrows\RestBundle\CollectionDecorator;

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

    public function __construct(
        array $configuration
    ) {
        $this->pageParameterName = $configuration['page_parameter_name'];
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
            if ($params->get('paramFetcher')->get($this->limitParameterName) === null ||
                $params->get('paramFetcher')->get($this->pageParameterName) === null
            ) {
                return $collection;
            }

            return new PaginationRepresentation(
                $collection,
                $params->get('_route'),
                $params->all(),
                $params->get('paramFetcher')->get($this->pageParameterName),
                $params->get('paramFetcher')->get($this->limitParameterName),
                null,
                $this->pageParameterName,
                $this->limitParameterName
            );
        } catch (InvalidArgumentException $exception) {
            return $collection;
        }
    }
}

<?php
namespace Ibrows\RestBundle\CollectionDecorator;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Representation\LastIdRepresentation;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\ParameterBag;

class LastIdDecorator extends AbstractDecorator
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

    /**
     * @var ParamFetcherInterface
     */
    protected $paramFetcher;

    public function __construct(
        array $configuration,
        ParamFetcherInterface $paramFetcher
    ) {
        $this->sortByParameterName = $configuration['sort_by_parameter_name'];
        $this->sortDirectionParameterName = $configuration['sort_direction_parameter_name'];
        $this->offsetIdParameterName = $configuration['offset_id_parameter_name'];
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

        $resources = $collection->getResources();
        $lastElement = end($resources);
        $offsetId = null;

        if ($lastElement instanceof ApiListableInterface) {
            $offsetId = $lastElement->getId();
        }

        $fetcher = $this->paramFetcher;

        try {
            $routeParameters = [];

            if ($params->has('_view')) {
                foreach ($params->get('_view')->getRouteParams() as $routeParamName) {
                    if($params->has($routeParamName)){
                        $routeParameters[$routeParamName] = $params->get($routeParamName);
                    }else {
                        if (null !== ($value = $fetcher->get($routeParamName))) {
                            $routeParameters[$routeParamName] = $value;
                        }
                    }
                }
            }

            $parameters = $params->all();
            $this->simplifyData($parameters);

            return new LastIdRepresentation(
                $collection,
                $params->get('_route'),
                $parameters,
                $fetcher ->get($this->limitParameterName),
                $this->limitParameterName,
                $offsetId,
                $this->offsetIdParameterName,
                $fetcher ->get($this->sortByParameterName),
                $this->sortByParameterName,
                $fetcher ->get($this->sortDirectionParameterName),
                $this->sortDirectionParameterName,
                false,
                $routeParameters
            );
        } catch (InvalidArgumentException $exception) {
            return $collection;
        }
    }
}

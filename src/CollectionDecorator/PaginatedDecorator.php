<?php
namespace Ibrows\RestBundle\CollectionDecorator;

use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Representation\PaginationRepresentation;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\ParameterBag;

class PaginatedDecorator implements DecoratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function decorate(ParameterBag $params, $collection)
    {
        if(
            !$collection instanceof CollectionRepresentation ||
            !$params->has('paramConverter') ||
            !$params->has('_route')
        ) {
            return $collection;
        }

        try {
            if (
                $params->get('paramConverter')->get('limit') === null ||
                $params->get('paramConverter')->get('page') === null
            ) {
                return $collection;
            }

            return new PaginationRepresentation(
                $collection,
                $params->get('_route'),
                $params->all(),
                $params->get('paramConverter')->get('page'),
                $params->get('paramConverter')->get('limit'),
                null
            );
        } catch (InvalidArgumentException $exception) {
            return $collection;
        }
    }
}
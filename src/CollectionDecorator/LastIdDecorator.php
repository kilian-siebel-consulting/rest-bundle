<?php
namespace Ibrows\RestBundle\CollectionDecorator;

use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Representation\LastIdRepresentation;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\ParameterBag;

class LastIdDecorator implements DecoratorInterface
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
            $resources = $collection->getResources();
            $lastElement = end($resources);

            if (!$lastElement || !$lastElement instanceof ApiListableInterface) {
                return $collection;
            }

            return new LastIdRepresentation(
                $collection,
                $params->get('_route'),
                $params->all(),
                $lastElement->getId(),
                'offsetId',
                $params->get('paramConverter')->get('limit'),
                'limit',
                $params->get('paramConverter')->get('sortBy'),
                $params->get('paramConverter')->get('sortDir')
            );
        } catch (InvalidArgumentException $exception) {
            return $collection;
        }
    }
}
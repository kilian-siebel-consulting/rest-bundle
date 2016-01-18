<?php
namespace Ibrows\RestBundle\CollectionDecorator;

use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Representation\OffsetRepresentation;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\ParameterBag;

class OffsetDecorator implements DecoratorInterface
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
            return new OffsetRepresentation(
                $collection,
                $params->get('_route'),
                $params->all(),
                $params->get('paramConverter')->get('offset'),
                $params->get('paramConverter')->get('limit')
            );
        } catch (InvalidArgumentException $exception) {
            return $collection;
        }
    }
}
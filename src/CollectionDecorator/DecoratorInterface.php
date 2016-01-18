<?php
namespace Ibrows\RestBundle\CollectionDecorator;

use Symfony\Component\HttpFoundation\ParameterBag;

interface DecoratorInterface
{
    /**
     * @param ParameterBag $params
     * @param mixed $collection
     * @return mixed the new response
     */
    public function decorate(ParameterBag $params, $collection);
}

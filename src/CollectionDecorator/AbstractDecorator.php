<?php


namespace Ibrows\RestBundle\CollectionDecorator;


use Ibrows\RestBundle\Model\ApiListableInterface;

abstract class AbstractDecorator implements DecoratorInterface
{
    protected function simplifyData(&$data){
        foreach($data as $key => $value){
            if($value instanceof ApiListableInterface){
                $data[$key] = $value->getId();
            }
        }
    }

}

<?php


namespace Ibrows\RestBundle\Exception;


use Exception;
use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;

class InvalidValueException extends OperationInvalidException
{
    public function __construct($message, Exception $previous)
    {
        parent::__construct($message, 400, $previous);
    }


}

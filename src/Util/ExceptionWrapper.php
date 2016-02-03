<?php

namespace Ibrows\RestBundle\Util;

use FOS\RestBundle\Util\ExceptionWrapper as BaseExceptionWrapper;

class ExceptionWrapper extends BaseExceptionWrapper
{
    private $exception;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);


        if (isset($data['exception'])) {
            $this->exception = $data['exception'];
        }
    }

    /**
     * @return mixed
     */
    public function getException()
    {
        return $this->exception;
    }
}

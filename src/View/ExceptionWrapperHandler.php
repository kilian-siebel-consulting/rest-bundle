<?php

namespace Ibrows\RestBundle\View;


use FOS\RestBundle\View\ExceptionWrapperHandlerInterface;

class ExceptionWrapperHandler implements ExceptionWrapperHandlerInterface
{
    /**
     * @var boolean
     */
    private $debug;

    /**
     * @param boolean $debug
     */
    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @inheritdoc
     */
    public function wrap($data)
    {
        if ($this->debug) {
            return new \Ibrows\RestBundle\Util\ExceptionWrapper($data);
        }
        
        return new \FOS\RestBundle\Util\ExceptionWrapper($data);
    }
}

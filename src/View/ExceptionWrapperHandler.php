<?php

namespace Ibrows\RestBundle\View;


use FOS\RestBundle\View\ExceptionWrapperHandlerInterface;

use Ibrows\RestBundle\Util\ExceptionWrapper as IbrowsExceptionWrapper;
use FOS\RestBundle\Util\ExceptionWrapper as FOSExceptionWrapper;

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
            return new IbrowsExceptionWrapper($data);
        }
        
        return new FOSExceptionWrapper($data);
    }
}

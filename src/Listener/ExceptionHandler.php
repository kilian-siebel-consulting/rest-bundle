<?php
namespace Ibrows\RestBundle\Listener;

use Exception;
use FOS\RestBundle\Serializer\Normalizer\ExceptionHandler as BaseExceptionHandler;
use FOS\RestBundle\Util\ExceptionValueMap;
use Ibrows\RestBundle\Exception\DisplayableException;
use JMS\Serializer\Context;

class ExceptionHandler extends BaseExceptionHandler
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * {@inheritDoc}
     */
    public function __construct(ExceptionValueMap $messagesMap, $debug)
    {
        parent::__construct($messagesMap, $debug);
        $this->debug = $debug;
    }

    /**
     * {@inheritDoc}
     */
    protected function convertToArray(Exception $exception, Context $context)
    {
        $result = parent::convertToArray($exception, $context);
        if($exception instanceof DisplayableException) {
            $result = array_merge($exception->toArray(), $result);
        }
        if($this->debug) {
            $result['file'] = $exception->getFile();
            $result['line'] = $exception->getLine();
            $result['stacktrace'] = $exception->getTraceAsString();
            $result['previous'] = $exception->getPrevious() === null
                ? null
                : $this->convertToArray($exception->getPrevious(), $context);
        }
        return $result;
    }
}

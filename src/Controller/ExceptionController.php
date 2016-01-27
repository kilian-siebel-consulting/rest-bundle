<?php

namespace Ibrows\RestBundle\Controller;

use FOS\RestBundle\View\ViewHandler;
use Ibrows\RestBundle\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use FOS\RestBundle\Controller\ExceptionController as BaseExceptionController;

class ExceptionController extends BaseExceptionController
{
    protected function getParameters(ViewHandler $viewHandler, $currentContent, $code, $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {
        $parameters = parent::getParameters($viewHandler, $currentContent, $code, $exception, $logger, $format);
        
        if ($exception instanceof FlattenException &&
            $exception->getDisplayableException()
        ) {
            $parameters['errors'] = $exception->getDisplayableException()->toArray();
        }

        return $parameters;
    }
}

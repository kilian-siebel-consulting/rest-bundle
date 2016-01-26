<?php

namespace Ibrows\RestBundle\Exception;

use Symfony\Component\Debug\Exception\FlattenException as BaseFlattenException;

class FlattenException extends BaseFlattenException
{
    /**
     * @var DisplayableException
     */
    private $displayableException;

    public static function create(\Exception $exception, $statusCode = null, array $headers = array())
    {
        $resultException =  parent::create($exception, $statusCode, $headers);

        if ($exception instanceof DisplayableException) {
            $resultException->displayableException = $exception;
        }
            
        return $resultException;
    }

    /**
     * @return DisplayableException
     */
    public function getDisplayableException()
    {
        return $this->displayableException;
    }
}

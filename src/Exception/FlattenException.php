<?php

namespace Ibrows\RestBundle\Exception;

use Symfony\Component\Debug\Exception\FlattenException as BaseFlattenException;

class FlattenException extends BaseFlattenException
{

    /**
     * @var array
     */
    private $errors;

    /**
     * @param \Exception $exception
     * @param null       $statusCode
     * @param array      $headers
     * @return static
     */
    public static function create(\Exception $exception, $statusCode = null, array $headers = array())
    {
        $resultException = parent::create($exception, $statusCode, $headers);

        if ($exception instanceof DisplayableException) {
            $resultException->errors = $exception->toArray();
        }

        return $resultException;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return $this->errors != null;
    }

}

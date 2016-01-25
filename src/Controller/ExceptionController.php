<?php

namespace Ibrows\RestBundle\Controller;


use JMS\Serializer\SerializerInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var bool
     */
    private $debug;

    /**
     * ExceptionController constructor.
     * @param SerializerInterface $serializer
     * @param boolean $debug
     */
    public function __construct(SerializerInterface $serializer, $debug)
    {
        $this->serializer = $serializer;
        $this->debug = $debug;
    }

    /**
     * @param Request              $request   The request
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     *
     * @return Response
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $object = [ 
            'error' => [
                'code' => $exception->getStatusCode(),
                'message' => $exception->getMessage(),
                'exception' => $exception->toArray()
            ]
        ];
        $responseContent = json_encode($object);
        
        return new Response($responseContent, $exception->getStatusCode());
    }
}

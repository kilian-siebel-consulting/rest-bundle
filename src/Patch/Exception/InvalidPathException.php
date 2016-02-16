<?php
namespace Ibrows\RestBundle\Patch\Exception;

use Exception;
use Ibrows\RestBundle\Patch\PointerInterface;

/**
 * Class InvalidPathException
 * @package Ibrows\RestBundle\Patch\Exception
 *
 * @codeCoverageIgnore
 *
 * {@inheritdoc}
 */
class InvalidPathException extends \RuntimeException
{
    /**
     * @var PointerInterface
     */
    private $pointer;

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * {@inheritdoc}
     */
    public function __construct(
        PointerInterface $pointer,
        $message = '',
        $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->pointer = $pointer;
    }

    /**
     * @return PointerInterface
     */
    public function getPointer()
    {
        return $this->pointer;
    }
}

<?php
namespace Ibrows\RestBundle\Patch\Exception;

use Exception;
use Ibrows\RestBundle\Patch\OperationInterface;
use Ibrows\RestBundle\Patch\ValueInterface;
use RuntimeException;

/**
 * Class OperationNotApplicableException
 * @package Ibrows\RestBundle\Patch\Exception
 *
 * @codeCoverageIgnore
 *
 * {@inheritDoc}
 */
class OperationNotApplicableException extends RuntimeException
{
    /**
     * @var ValueInterface
     */
    private $value;

    /**
     * @var OperationInterface
     */
    private $operation;

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param ValueInterface     $value
     * @param OperationInterface $operation
     * {@inheritdoc}
     */
    public function __construct(
        OperationInterface $operation,
        ValueInterface $value,
        $message = '',
        $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->value = $value;
        $this->operation = $operation;
    }

    /**
     * @return ValueInterface
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return OperationInterface
     */
    public function getOperation()
    {
        return $this->operation;
    }
}

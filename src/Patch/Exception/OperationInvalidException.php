<?php
namespace Ibrows\RestBundle\Patch\Exception;

use InvalidArgumentException;

/**
 * Class OperationInvalidException
 * @package Ibrows\RestBundle\Patch\Exception
 *
 * @codeCoverageIgnore
 *
 * {@inheritdoc}
 */
class OperationInvalidException extends InvalidArgumentException
{
    const
        MISSING_PROPERTY_MESSAGE = 'The property "%s" must be provided for every operation.',
        MISSING_SPECIALISED_PROPERTY_MESSAGE = 'The property "%s" must be provided for the %s operation.',
        INVALID_OPERATION = 'Couldn\'t find an applier for the operation "%s".';
}

<?php
namespace Ibrows\RestBundle\Patch\Exception;

use Exception;
use Ibrows\RestBundle\Patch\AddressInterface;
use Ibrows\RestBundle\Patch\PointerInterface;

/**
 * Class ResolvePathException
 * @package Ibrows\RestBundle\Patch\Exception
 *
 * @codeCoverageIgnore
 *
 * {@inheritdoc}
 */
class PropertyNullPathException extends InvalidPathException
{
    const MESSAGE = 'Could not change on path "%s" because value is null ( exists not ).';

    /**
     * @var string
     */
    private $token;

    /**
     * @var AddressInterface
     */
    private $address;

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * {@inheritdoc}
     * @param AddressInterface $address
     * @param string|null      $token
     * @throws InvalidPathException
     */
    public function __construct(
        AddressInterface $address,
        PointerInterface $pointer,
        $token = null,
        $code = 0,
        Exception $previous = null
    ) {
        if (!$token) {
            $token = $pointer->lastToken();
        }

        parent::__construct(
            $pointer,
            sprintf(self::MESSAGE, $token),
            $code,
            $previous
        );
        $this->address = $address;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return AddressInterface
     */
    public function getAddress()
    {
        return $this->address;
    }
}

<?php
namespace Ibrows\RestBundle\Patch;

class RootPointer implements PointerInterface
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static;
    }

    /**
     * {@inheritdoc}
     */
    public function tokens()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function lastToken()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function resolve(& $object, array $options = [])
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        throw new \RuntimeException('resolve can\'t be called on RootPointer');
    }

    /**
     * {@inheritdoc}
     */
    public function path()
    {
        return null;
    }
}

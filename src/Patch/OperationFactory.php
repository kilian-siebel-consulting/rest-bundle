<?php
namespace Ibrows\RestBundle\Patch;

class OperationFactory implements OperationFactoryInterface
{
    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * {@inheritdoc}
     */
    public function create(
        $name,
        PointerInterface $pathPathPointer,
        PointerInterface $fromPointer = null,
        $value = null,
        array $parameters = []
    ) {
        return new Operation($name, $pathPathPointer, $fromPointer, $value, $parameters);
    }
}

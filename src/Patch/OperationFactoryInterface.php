<?php
namespace Ibrows\RestBundle\Patch;

interface OperationFactoryInterface
{
    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param string                $name
     * @param PointerInterface      $pathPointer
     * @param PointerInterface|null $fromPointer
     * @param mixed|null            $value
     * @param mixed[]               $parameters
     * @return OperationInterface
     */
    public function create(
        $name,
        PointerInterface $pathPointer,
        PointerInterface $fromPointer = null,
        $value = null,
        array $parameters = []
    );
}

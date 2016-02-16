<?php
namespace Ibrows\RestBundle\Patch;

interface OperationInterface
{
    /**
     * @return string
     */
    public function operation();

    /**
     * @return PointerInterface
     */
    public function pathPointer();

    /**
     * @return PointerInterface|null
     */
    public function fromPointer();

    /**
     * @return mixed|null
     */
    public function value();

    /**
     * @return mixed[]
     */
    public function parameters();
}

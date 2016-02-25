<?php
namespace Ibrows\RestBundle\Patch;

class Operation implements OperationInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var PointerInterface
     */
    private $pathPointer;

    /**
     * @var PointerInterface|null
     */
    private $fromPointer;

    /**
     * @var mixed|null
     */
    private $value;

    /**
     * @var mixed[]
     */
    private $parameters;

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * Operation constructor.
     * @param string                $name
     * @param PointerInterface      $pathPointer
     * @param PointerInterface|null $fromPointer
     * @param mixed|null            $value
     * @param mixed[]               $parameters
     */
    public function __construct(
        $name,
        PointerInterface $pathPointer,
        PointerInterface $fromPointer = null,
        $value = null,
        array $parameters = []
    ) {
        $this->name = $name;
        $this->pathPointer = $pathPointer;
        $this->fromPointer = $fromPointer;
        $this->value = $value;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function operation()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function pathPointer()
    {
        return $this->pathPointer;
    }

    /**
     * {@inheritdoc}
     */
    public function fromPointer()
    {
        return $this->fromPointer;
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function parameters()
    {
        return $this->parameters;
    }
}

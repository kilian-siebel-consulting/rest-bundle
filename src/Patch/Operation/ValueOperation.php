<?php
namespace Ibrows\RestBundle\Patch\Operation;

use Ibrows\RestBundle\Patch\Operation;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

abstract class ValueOperation extends Operation
{
    /**
     * @var mixed
     * @Expose
     * @Type("ibrows_rest_resource")
     */
    private $value;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return ValueOperation
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

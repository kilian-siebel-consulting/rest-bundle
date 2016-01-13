<?php
namespace Ibrows\RestBundle\Patch;

use JMS\Serializer\Annotation\Discriminator;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class Operation
 *
 * @package Ibrows\RestBundle\Patch
 *
 * @Discriminator(
 *     field="op",
 *     map = {
 *         "change" = "Ibrows\RestBundle\Patch\Operation\Change",
 *         "clear" = "Ibrows\RestBundle\Patch\Operation\Clear",
 *     },
 * )
 * @ExclusionPolicy("all")
 */
abstract class Operation
{
    /**
     * @var string
     * @Type("string")
     * @Expose
     * @NotBlank
     */
    private $path;

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
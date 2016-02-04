<?php
namespace Ibrows\RestBundle\Patch;

use JMS\Serializer\Annotation\Discriminator;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Metadata\PropertyMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class Operation
 *
 * @package Ibrows\RestBundle\Patch
 *
 * @Discriminator(
 *     field="op",
 *     map = {
 *         "replace" = "Ibrows\RestBundle\Patch\Operation\Replace",
 *         "remove" = "Ibrows\RestBundle\Patch\Operation\Remove",
 *     },
 * )
 * @ExclusionPolicy("all")
 */
abstract class Operation implements OperationInterface
{
    /**
     * @var string
     * @Type("string")
     * @Expose
     * @NotBlank
     */
    private $path;

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }
}

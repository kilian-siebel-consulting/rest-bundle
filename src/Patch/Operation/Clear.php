<?php
namespace Ibrows\RestBundle\Patch\Operation;

use Ibrows\RestBundle\Patch\Operation;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * Class Change
 *
 * @package Ibrows\RestBundle\Patch\Operation
 * @ExclusionPolicy("all")
 */
class Clear extends Operation
{}
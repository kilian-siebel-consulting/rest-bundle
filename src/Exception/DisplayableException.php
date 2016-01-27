<?php

namespace Ibrows\RestBundle\Exception;

interface DisplayableException
{
    /**
     * @return array
     */
    public function toArray();
}

<?php


namespace Ibrows\RestBundle\Model;


interface ApiPositionableInterface
{
    /**
     * @param int $position
     */
    public function setPosition( $position );

    /**
     * @return int
     */
    public function getPosition();

}

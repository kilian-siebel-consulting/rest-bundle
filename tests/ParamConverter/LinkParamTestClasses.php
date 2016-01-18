<?php
namespace Ibrows\RestBundle\Tests\ParamConverter;

use Doctrine\Common\Collections\ArrayCollection;
class Car
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var array
     */
    private $wheels;

    /**
     * @var array
     */
    private $doors;


    public function __construct($id)
    {
        $this->id = $id;
        $this->wheels = new ArrayCollection();
        $this->doors = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getWheels()
    {
        return $this->wheels;
    }

    /**
     * @param array $wheels
     */
    public function setWheels($wheels = [])
    {
        $this->wheels = $wheels;
    }

    /**
     * @return array
     */
    public function getDoors()
    {
        return $this->doors;
    }

    /**
     * @param array $doors
     */
    public function setDoors($doors = [])
    {
        $this->doors = $doors;
    }
}

class Wheel
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}

class Door
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


}
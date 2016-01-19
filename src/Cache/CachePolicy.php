<?php
namespace Ibrows\RestBundle\Cache;

class CachePolicy
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     * In seconds
     */
    protected $timeToLife;

    /**
     * @var string
     */
    protected $type;

    const TYPE_PRIVATE = "private";
    const TYPE_PUBLIC = "public";
    const TYPE_NO_CACHE = "nocache";
    const TYPE_NO_STORE = "nostore";

    /**
     * CachePolicy constructor.
     * @param string $name
     * @param int $timeToLife
     * @param bool $privateCache
     */
    public function __construct($name, $timeToLife, $type)
    {
        $this->name = $name;
        $this->timeToLife = $timeToLife;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getTimeToLife()
    {
        return $this->timeToLife;
    }

    /**
     * @param int $timeToLife
     */
    public function setTimeToLife($timeToLife)
    {
        $this->timeToLife = $timeToLife;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
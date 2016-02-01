<?php

namespace Ibrows\RestBundle\Tests\Integration\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="fix_article")
 */
class Article
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=8)
     */
    private $title;


    /**
     * @var $someDate \DateTime
     * @ORM\Column(name="some_date", type="datetime", nullable=true)
     */
    private $someDate;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return \DateTime
     */
    public function getSomeDate()
    {
        return $this->someDate;
    }

    /**
     * @param \DateTime $someDate
     */
    public function setSomeDate(\DateTime $someDate = null)
    {
        $this->someDate = $someDate;
    }
}

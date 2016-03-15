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
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=8)
     */
    private $title;

    /**
     * @var $someDate \DateTime
     * @ORM\Column(name="some_date", type="datetime", nullable=true)
     */
    private $someDate;

    /**
     * @var Comment[]
     * @ORM\OneToMany(targetEntity="Ibrows\RestBundle\Tests\Integration\Entity\Comment", mappedBy="article")
     */
    private $comments;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
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

    /**
     * @return Comment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment[] $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }
}

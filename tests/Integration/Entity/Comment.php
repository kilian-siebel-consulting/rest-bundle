<?php

namespace Ibrows\RestBundle\Tests\Integration\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity
 * @ORM\Table(name="fix_comment")
 */
class Comment
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(length=128)
     */
    private $subject;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @ReadOnly
     */
    private $hidden;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @SerializedName("fakeName")
     */
    private $overriddenName;

    /**
     * @var Article|null
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="comments", cascade="persist")
     * @ORM\JoinColumn(nullable=true)
     */
    private $article;


    /**
     * @param Article $article
     */
    public function setArticle(Article $article = null)
    {
        $this->article = $article;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}

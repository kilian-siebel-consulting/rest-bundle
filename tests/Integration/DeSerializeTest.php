<?php

namespace Ibrows\RestBundle\Tests\Integration;

use Doctrine\ORM\Query;
use Ibrows\RestBundle\Tests\Integration\Entity\Article;
use Ibrows\RestBundle\Tests\Integration\Entity\Comment;

class DeSerializeTest extends WebTestCase
{
    public function testStrictDeserializeComment()
    {
        $data = json_encode(array('article' => '/api/v1/en_US/articles/1', 'message' => 'blah'));
        $serializer = self::getContainer()->get('jms_serializer');
        $data = $serializer->deserialize($data, Comment::class, 'json');
        $this->assertInstanceOf(Comment::class, $data);
        $this->assertInstanceOf(Article::class, $data->getArticle());
    }

    public function testStrictNoDeserializeComment()
    {
        $data = json_encode(array('article' => null, 'message' => 'blah'));
        $serializer = self::getContainer()->get('jms_serializer');
        $data = $serializer->deserialize($data, Comment::class, 'json');
        $this->assertInstanceOf(Comment::class, $data);
        $this->assertNull($data->getArticle());
    }
}

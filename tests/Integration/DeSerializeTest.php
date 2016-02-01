<?php

namespace Ibrows\RestBundle\Tests\Integration;

use Doctrine\ORM\Query;
use Ibrows\RestBundle\Tests\app\AppKernel;
use Ibrows\RestBundle\Tests\Integration\Entity\Article;
use Ibrows\RestBundle\Tests\Integration\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeSerializeTest extends WebTestCase

{

    /**
     * @var ContainerInterface
     */
    protected static $container = null;


    public function testDeserialize()
    {
        $data = json_encode('/articles/1');
        $data = $this->JMSDeserialize($data);
        $this->assertInstanceOf(Article::class, $data);
    }


    public function testNoDeserialize()
    {
        $data = json_encode('foobar');
        $data = $this->JMSDeserialize($data);
        $this->assertEquals('foobar', $data);
    }

    public function testDeserialize2()
    {
        $data = json_encode(array('article' => '/articles/1', 'message' => 'blah'));
        $serializer = self::$container->get('jms_serializer');
        $data = $serializer->deserialize($data, Comment::class, 'json');
        $this->assertInstanceOf(Comment::class, $data);
    }

    private function JMSDeserialize($data)
    {
        $serializer = self::$container->get('jms_serializer');
        return $serializer->deserialize($data, self::$container->getParameter('ibrows_rest.resource_deserialization.type_name_weak'), 'json');
    }


    protected function setUp()
    {
        static::bootKernel();
        static::$container = static::$kernel->getContainer();
    }

    protected static function createKernel(array $options = array())
    {
        require_once __DIR__ . '/app/AppKernel.php';
        return new AppKernel(
            'config/config.yml',
            'test',
            true
        );
    }

    protected function tearDown()
    {

    }
}

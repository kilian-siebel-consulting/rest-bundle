<?php
namespace Ibrows\RestBundle\Tests\Integration\ParamConverter;

use Ibrows\RestBundle\Tests\Integration\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class CommentParamConverter implements ParamConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $comment = new Comment();
        $comment->setSubject('old subject');

        $request->attributes->set($configuration->getName(), $comment);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === Comment::class;
    }
}

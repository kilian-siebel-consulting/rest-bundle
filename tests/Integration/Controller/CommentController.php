<?php
namespace Ibrows\RestBundle\Tests\Integration\Controller;

use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Route;
use Ibrows\RestBundle\Tests\Integration\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class CommentController
 * @package Ibrows\RestBundle\Tests\Integration\Controller
 *
 * @Route("/comments")
 */
class CommentController
{
    /**
     * @param Comment $comment
     * @ParamConverter(
     *     name="comment",
     *     converter="patch",
     *     class="Ibrows\RestBundle\Tests\Integration\Entity\Comment",
     *     options={
     *         "source" = "test.comment"
     *     }
     * )
     *
     * @Patch("/{comment}")
     *
     * @return Comment
     */
    public function patchAction(Comment $comment)
    {
        return $comment;
    }

    /**
     * @param Comment $comment
     * @ParamConverter(
     *     name="comment",
     *     converter="patch",
     *     class="Ibrows\RestBundle\Tests\Integration\Entity\Comment",
     *     options={
     *         "source" = "test.comment",
     *         "deserializationContext" = {
     *             "groups" = { "someGroup", },
     *         },
     *     }
     * )
     *
     * @Patch("/{comment}/groups")
     *
     * @return Comment
     */
    public function groupPatchAction(Comment $comment)
    {
        return $comment;
    }

    /**
     * @param Comment $comment
     * @ParamConverter(
     *     name="comment",
     *     converter="patch",
     *     class="Ibrows\RestBundle\Tests\Integration\Entity\Comment",
     *     options={
     *         "source" = "test.comment",
     *         "deserializationContext" = {
     *             "version" = 7,
     *         },
     *     }
     * )
     *
     * @Patch("/{comment}/version")
     *
     * @return Comment
     */
    public function versionPatchAction(Comment $comment)
    {
        return $comment;
    }
}

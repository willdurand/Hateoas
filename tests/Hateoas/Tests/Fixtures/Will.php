<?php

namespace Hateoas\Tests\Fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}))
 * @Hateoas\Relation(
 *     "post",
 *     href = "expr(link(object.getPost(), 'self', true))"
 * )
 */
class Will
{
    private $id;

    private $post;

    public function __construct($id, Post $post = null)
    {
        $this->id   = $id;
        $this->post = $post;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPost()
    {
        return $this->post;
    }
}

<?php

namespace Hateoas\Tests\Fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = @Hateoas\Route("post_get", parameters = {"id" = "expr(object.getId())"}))
 */
class Post
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}

<?php

namespace Hateoas\Tests\Fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}))
 */
class Will
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

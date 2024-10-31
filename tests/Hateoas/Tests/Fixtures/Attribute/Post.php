<?php

declare(strict_types=1);

namespace Hateoas\Tests\Fixtures\Attribute;

use Hateoas\Configuration\Annotation as Hateoas;

#[Hateoas\Relation(
    'self',
    href: new Hateoas\Route(
        'post_get',
        parameters: ['id' => 'expr(object.getId())'],
    ),
)]
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

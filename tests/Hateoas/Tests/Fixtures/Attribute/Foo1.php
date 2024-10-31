<?php

declare(strict_types=1);

namespace Hateoas\Tests\Fixtures\Attribute;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

#[Hateoas\Relation(
    'self1',
    href: 'foo1',
    embedded: 'foo1',
)]
class Foo1
{
    #[Serializer\Inline]
    public $inline;
}

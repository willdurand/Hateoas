<?php

declare(strict_types=1);

namespace Hateoas\Tests\Fixtures\Attribute;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

#[Hateoas\Relation(
    'self2',
    href: 'foo2',
    embedded: 'foo2',
)]
class Foo2
{
    #[Serializer\Inline]
    public $inline;
}

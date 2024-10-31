<?php

declare(strict_types=1);

namespace Hateoas\Tests\Fixtures\Attribute;

use Hateoas\Configuration\Annotation as Hateoas;

#[Hateoas\Relation(
    'self3',
    href: 'foo3',
    embedded: 'foo3',
)]
class Foo3
{
}

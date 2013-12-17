<?php

namespace Hateoas\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     "self2",
 *     href = "foo2",
 *     embedded = "foo2"
 * )
 */
class Foo2
{
    /**
     * @Serializer\Inline
     */
    public $inline;
}

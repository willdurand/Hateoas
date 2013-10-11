<?php

namespace tests\fixtures;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     "self1",
 *     href = "foo1",
 *     embed = "foo1"
 * )
 */
class Foo1
{
    /**
     * @Serializer\Inline
     */
    public $inline;
}

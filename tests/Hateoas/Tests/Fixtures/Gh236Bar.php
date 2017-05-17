<?php

namespace Hateoas\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class Gh236Bar
{
    /**
     * @Serializer\Expose()
     */
    public $xxx = 'yyy';

    /**
     * @Serializer\Expose()
     * @Serializer\SkipWhenEmpty()
     */
    public $inner;
}

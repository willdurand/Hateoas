<?php

declare(strict_types=1);

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

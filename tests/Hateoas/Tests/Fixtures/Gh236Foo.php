<?php

namespace Hateoas\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class Gh236Foo
{
    /**
     * @Serializer\Expose()
     * @Serializer\MaxDepth(1)
     */
    public $bar;

    public function __construct()
    {
        $this->bar = new Gh236Bar();
    }
}

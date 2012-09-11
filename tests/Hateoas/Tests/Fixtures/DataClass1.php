<?php

namespace Hateoas\Tests\Fixtures;

class DataClass1
{
    public $content;

    public function __construct($content)
    {
        $this->content = $content;
    }
}

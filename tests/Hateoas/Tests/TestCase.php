<?php

namespace Hateoas\Tests;

use Hautelook\Frankenstein\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public static function rootPath()
    {
        return __DIR__;
    }

    protected function json($string)
    {
        return $this->string(
            json_encode(json_decode($string), JSON_PRETTY_PRINT)
        );
    }
}

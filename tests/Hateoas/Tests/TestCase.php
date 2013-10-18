<?php

namespace Hateoas\Tests;

use Hautelook\Frankenstein\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public static function rootPath()
    {
        return __DIR__;
    }
}

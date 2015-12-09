<?php

namespace Hateoas\Tests;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    public static function rootPath()
    {
        return __DIR__;
    }

    protected function json($string)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            throw new \PHPUnit_Framework_IncompleteTestError('This test requires PHP 5.4+');
        }

        return json_encode(json_decode($string), JSON_PRETTY_PRINT);
    }
}

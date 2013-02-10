<?php

namespace Hateoas\Tests\Builder;

use Hateoas\Builder\CallableLinkBuilder;
use Hateoas\Tests\TestCase;

class CallableLinkBuilderTest extends TestCase
{
    public function testCreateWithClosure()
    {
        $builder = new CallableLinkBuilder(function ($route, $parameters) {
            return 'foo.bar' === $route ? '/foo/bar' : null;
        });

        $link = $builder->create('foo.bar');

        $this->assertInstanceOf('Hateoas\Link', $link);
        $this->assertEquals('/foo/bar', $link->getHref());
        $this->assertEquals('self', $link->getRel());
        $this->assertNull($link->getType());
    }

    public function testCreateWithClosureWithUnknownRoute()
    {
        $builder = new CallableLinkBuilder(function ($route, $parameters) {
            return 'foo.bar' === $route ? '/foo/bar' : null;
        });

        $link = $builder->create('hello');

        $this->assertInstanceOf('Hateoas\Link', $link);
        $this->assertNull($link->getHref());
        $this->assertEquals('self', $link->getRel());
        $this->assertNull($link->getType());
    }

    public function testCreateWithInstanceAndMethod()
    {
        $builder = new CallableLinkBuilder(array($this, 'generateRoute'));

        $link = $builder->create('foo.bar');

        $this->assertInstanceOf('Hateoas\Link', $link);
        $this->assertEquals('/foo/bar', $link->getHref());
        $this->assertEquals('self', $link->getRel());
        $this->assertNull($link->getType());
    }

    public function testCreateWithClassAndMethod()
    {
        $builder = new CallableLinkBuilder(array(get_class($this), 'generateRouteStatic'));

        $link = $builder->create('foo.bar', array(), 'self', 'application/vnd.hateoas.test');

        $this->assertInstanceOf('Hateoas\Link', $link);
        $this->assertEquals('/foo/bar', $link->getHref());
        $this->assertEquals('self', $link->getRel());
        $this->assertEquals('application/vnd.hateoas.test', $link->getType());
    }

    public function generateRoute($route, $parameters)
    {
        return 'foo.bar' === $route ? '/foo/bar' : null;
    }

    public static function generateRouteStatic($route, $parameters)
    {
        return 'foo.bar' === $route ? '/foo/bar' : null;
    }
}

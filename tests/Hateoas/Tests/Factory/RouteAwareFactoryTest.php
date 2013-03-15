<?php

namespace Hateoas\Tests\Factory;

use Hateoas\Factory\Config\ArrayConfig;
use Hateoas\Factory\Definition\RouteLinkDefinition;
use Hateoas\Factory\RouteAwareFactory;
use Hateoas\Tests\TestCase;

class RouteAwareFactoryTest extends TestCase
{
    public function testGetResourceDefinition()
    {
        $factory = new RouteAwareFactory(new ArrayConfig(array(
            'foobar' => array(
                'links' => array(
                    array('rel' => 'foo', 'type' => 'bar', 'route' => 'foo.get')
                ),
            ),
        ), array()));

        $def = $factory->getResourceDefinition('foobar');
        $this->assertInstanceOf('Hateoas\Factory\Definition\ResourceDefinition', $def);
        $this->assertEquals('foobar', $def->getClass());

        $links = $def->getLinks();
        $this->assertCount(1, $links);
        $this->assertInstanceOf('Hateoas\Factory\Definition\RouteLinkDefinition', $links[0]);
        $this->assertEquals('foo', $links[0]->getRel());
        $this->assertEquals('bar', $links[0]->getType());
        $this->assertEquals('foo.get', $links[0]->getRoute());
    }

    public function testGetResourceDefinitionWithLinkDefinition()
    {
        $linkDef = new RouteLinkDefinition('foo.bar',  array(), 'foo', 'bar');
        $factory = new RouteAwareFactory(new ArrayConfig(array(
            'foobar' => array(
                'links' => array(
                    $linkDef,
                ),
            ),
        )));

        $def = $factory->getResourceDefinition('foobar');
        $this->assertInstanceOf('Hateoas\Factory\Definition\ResourceDefinition', $def);
        $this->assertEquals('foobar', $def->getClass());

        $links = $def->getLinks();
        $this->assertCount(1, $links);
        $this->assertInstanceOf('Hateoas\Factory\Definition\RouteLinkDefinition', $links[0]);
        $this->assertEquals('foo', $links[0]->getRel());
        $this->assertEquals('bar', $links[0]->getType());
        $this->assertEquals('foo.bar', $links[0]->getRoute());

        $this->assertSame($linkDef, $links[0]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A link definition should define a "route" value.
     */
    public function testGetResourceDefinitionWithoutRoute()
    {
        $factory = new RouteAwareFactory(new ArrayConfig(array(
            'foobar' => array(
                'links' => array(
                    array('rel' => 'foo', 'type' => 'bar')
                ),
            ),
        ), array()));

        $factory->getResourceDefinition('foobar');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A link definition should define a "rel" value.
     */
    public function testGetResourceDefinitionWithoutRel()
    {
        $factory = new RouteAwareFactory(new ArrayConfig(array(
            'foobar' => array(
                'links' => array(
                    array('route' => 'foo', 'type' => 'bar')
                ),
            ),
        ), array()));

        $factory->getResourceDefinition('foobar');
    }
}

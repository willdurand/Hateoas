<?php

namespace Hateoas\Tests\Factory;

use Hateoas\Factory\Config\ArrayConfig;
use Hateoas\Factory\Definition\EmbedDefinition;
use Hateoas\Factory\Definition\LinkDefinition;
use Hateoas\Factory\Factory;
use Hateoas\Tests\Fixtures\DummyClass;
use Hateoas\Tests\TestCase;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class FactoryTest extends TestCase
{
    public function testGetResourceDefinition()
    {
        $factory = new Factory(new ArrayConfig(array(
            'foobar' => array(
                'links' => array(
                    array('rel' => 'foo', 'type' => 'bar')
                ),
            ),
        )));

        $def = $factory->getResourceDefinition('foobar');
        $this->assertInstanceOf('Hateoas\Factory\Definition\ResourceDefinition', $def);
        $this->assertEquals('foobar', $def->getClass());

        $links = $def->getLinks();
        $this->assertCount(1, $links);
        $this->assertInstanceOf('Hateoas\Factory\Definition\LinkDefinition', $links[0]);
        $this->assertEquals('foo', $links[0]->getRel());
        $this->assertEquals('bar', $links[0]->getType());
    }

    public function testGetResourceDefinitionWithLinkDefinition()
    {
        $linkDef = new LinkDefinition('foo', 'bar');
        $factory = new Factory(new ArrayConfig(array(
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
        $this->assertInstanceOf('Hateoas\Factory\Definition\LinkDefinition', $links[0]);
        $this->assertEquals('foo', $links[0]->getRel());
        $this->assertEquals('bar', $links[0]->getType());

        $this->assertSame($linkDef, $links[0]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The "links" definition should be an array in "foobar".
     */
    public function testGetResourceDefinitionWithBadLinkDefinition()
    {
        $linkDef = new LinkDefinition('foo', 'bar');
        $factory = new Factory(new ArrayConfig(array(
            'foobar' => array(
                'links' => 'foo'
            ),
        )));

        $def = $factory->getResourceDefinition('foobar');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A link definition should be an array in "foobar".
     */
    public function testGetResourceDefinitionWithBadLinkDefinition2()
    {
        $linkDef = new LinkDefinition('foo', 'bar');
        $factory = new Factory(new ArrayConfig(array(
            'foobar' => array(
                'links' => array('foo'),
            ),
        )));

        $def = $factory->getResourceDefinition('foobar');
    }

    public function testGetResourceDefinitionWithEmbedDefinition()
    {
        $embedDefinition = new EmbedDefinition('foo', 'bar');
        $factory = new Factory(new ArrayConfig(array(
            'foobar' => array(
                'embeds' => array(
                    $embedDefinition,
                ),
            ),
        )));

        $def = $factory->getResourceDefinition('foobar');
        $this->assertInstanceOf('Hateoas\Factory\Definition\ResourceDefinition', $def);
        $this->assertEquals('foobar', $def->getClass());

        $embed = $def->getEmbeds();
        $this->assertCount(1, $embed);
        $this->assertInstanceOf('Hateoas\Factory\Definition\EmbedDefinition', $embed[0]);
        $this->assertEquals('foo', $embed[0]->getName());
        $this->assertEquals('getBar', $embed[0]->getAccessor());

        $this->assertSame($embedDefinition, $embed[0]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The "embeds" definition should be an array in "foobar".
     */
    public function testGetResourceDefinitionWithBadEmbeddeDefinition()
    {
        $embedef = new EmbedDefinition('foo', 'bar');
        $factory = new Factory(new ArrayConfig(array(
            'foobar' => array(
                'embeds' => 'foo'
            ),
        )));

        $def = $factory->getResourceDefinition('foobar');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage An embed definition should be an array in "foobar".
     */
    public function testGetResourceDefinitionWithBadEmbedDfinition2()
    {
        $embedef = new EmbedDefinition('foo', 'bar');
        $factory = new Factory(new ArrayConfig(array(
            'foobar' => array(
                'embeds' => array('foo'),
            ),
        )));

        $def = $factory->getResourceDefinition('foobar');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetResourceWithUnknownClass()
    {
        $factory = new Factory(new ArrayConfig(array(
            'foobar' => array(
                'links' => array(
                    array('rel' => 'foo', 'type' => 'bar')
                ),
            ),
        )));

        $factory->getResourceDefinition('nonexistentclass');
    }

    public function testGetResourceWithObject()
    {
        $linkDef = new LinkDefinition('foo', 'bar');
        $factory = new Factory(new ArrayConfig(array(
            'Hateoas\Tests\Fixtures\DummyClass' => array(
                'links' => array(
                    $linkDef,
                ),
            ),
        )));

        $def = $factory->getResourceDefinition(new DummyClass());

        $this->assertInstanceOf('Hateoas\Factory\Definition\ResourceDefinition', $def);
        $this->assertEquals('Hateoas\Tests\Fixtures\DummyClass', $def->getClass());

        $links = $def->getLinks();
        $this->assertCount(1, $links);
        $this->assertInstanceOf('Hateoas\Factory\Definition\LinkDefinition', $links[0]);
        $this->assertEquals('foo', $links[0]->getRel());
        $this->assertEquals('bar', $links[0]->getType());

        $this->assertSame($linkDef, $links[0]);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetResourceWithUnknownObject()
    {
        $factory = new Factory(new ArrayConfig(array(
            'foobar' => array(
                'links' => array(
                    array('rel' => 'foo', 'type' => 'bar')
                ),
            ),
        )));

        $factory->getResourceDefinition(new DummyClass());
    }

    public function testGetCollectionDefinition()
    {
        $factory = new Factory(new ArrayConfig(
            array(), // entities
            array(
                'foobar' => array(
                    'links' => array(
                        array('rel' => 'foo', 'type' => 'bar')
                    )
                ),
            )
        ));

        $def = $factory->getCollectionDefinition('foobar');
        $this->assertInstanceOf('Hateoas\Factory\Definition\CollectionDefinition', $def);
        $this->assertEquals('foobar', $def->getClass());

        $links = $def->getLinks();
        $this->assertCount(1, $links);
        $this->assertInstanceOf('Hateoas\Factory\Definition\LinkDefinition', $links[0]);
        $this->assertEquals('foo', $links[0]->getRel());
        $this->assertEquals('bar', $links[0]->getType());
    }

    public function testGetCollectionDefinitionWithLinkDefinition()
    {
        $linkDef = new LinkDefinition('foo', 'bar');
        $factory = new Factory(new ArrayConfig(
            array(), // entities
            array(
                'foobar' => array(
                    'links' => array(
                        $linkDef,
                    )
                ),
            )
        ));

        $def = $factory->getCollectionDefinition('foobar');
        $this->assertInstanceOf('Hateoas\Factory\Definition\CollectionDefinition', $def);
        $this->assertEquals('foobar', $def->getClass());

        $links = $def->getLinks();
        $this->assertCount(1, $links);
        $this->assertInstanceOf('Hateoas\Factory\Definition\LinkDefinition', $links[0]);
        $this->assertEquals('foo', $links[0]->getRel());
        $this->assertEquals('bar', $links[0]->getType());

        $this->assertSame($linkDef, $links[0]);
    }
}

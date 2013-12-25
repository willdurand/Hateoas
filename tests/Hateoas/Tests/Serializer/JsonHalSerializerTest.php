<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\HateoasBuilder;
use Hateoas\Model\Embedded;
use Hateoas\Model\Link;
use Hateoas\Serializer\JsonHalSerializer;
use Hateoas\Tests\Fixtures\AdrienBrault;
use Hateoas\Tests\Fixtures\Foo1;
use Hateoas\Tests\Fixtures\Foo2;
use Hateoas\Tests\Fixtures\Foo3;
use Hateoas\Tests\TestCase;

class JsonHalSerializerTest extends TestCase
{
    public function testSerializeLinks()
    {
        $links = array(
            new Link('self', '/users/42', array('awesome' => 'exactly')),
            new Link('foo', '/bar'),
            new Link('foo', '/baz'),
            new Link('bar', '/foo'),
            new Link('bar', '/baz'),
            new Link('bar', '/buzz'),
        );

        $expectedSerializedLinks = array(
            'self' => array(
                'href' => '/users/42',
                'awesome' => 'exactly',
            ),
            'foo' => array(
                array('href' => '/bar'),
                array('href' => '/baz'),
            ),
            'bar' => array(
                array('href' => '/foo'),
                array('href' => '/baz'),
                array('href' => '/buzz'),
            ),
        );

        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');

        $jsonSerializationVisitorProphecy = $this->prophesize('JMS\Serializer\JsonSerializationVisitor');
        $jsonSerializationVisitorProphecy
            ->addData('_links', $expectedSerializedLinks)
            ->shouldBeCalledTimes(1)
        ;

        $jsonHalSerializer = new JsonHalSerializer();
        $jsonHalSerializer->serializeLinks(
            $links,
            $jsonSerializationVisitorProphecy->reveal(),
            $contextProphecy->reveal()
        );
    }

    public function testSerializeEmbeddeds()
    {
        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');
        $contextProphecy
            ->accept(array('name' => 'John'))
            ->willReturnArgument()
        ;

        $embeddeds = array(
            new Embedded('friend', array('name' => 'John')),
        );

        $expectedEmbeddedded = array(
            'friend' => array('name' => 'John'),
        );

        $jsonSerializationVisitorProphecy = $this->prophesize('JMS\Serializer\JsonSerializationVisitor');
        $jsonSerializationVisitorProphecy
            ->addData('_embedded', $expectedEmbeddedded)
            ->shouldBeCalledTimes(1)
        ;

        $jsonHalSerializer = new JsonHalSerializer();
        $jsonHalSerializer->serializeEmbeddeds(
            $embeddeds,
            $jsonSerializationVisitorProphecy->reveal(),
            $contextProphecy->reveal()
        );
    }

    public function testSerializeCuriesWithOneLinkShouldBeAnArray()
    {
        $links = array(
            new Link('self',   '/users/42'),
            new Link('curies', '/rels/{rel}', array('name' => 'p')),
        );

        $expectedSerializedLinks = array(
            'self' => array(
                'href' => '/users/42',
            ),
            'curies' => array(
                array(
                    'href' => '/rels/{rel}',
                    'name' => 'p',
                ),
            ),
        );

        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');

        $jsonSerializationVisitorProphecy = $this->prophesize('JMS\Serializer\JsonSerializationVisitor');
        $jsonSerializationVisitorProphecy
            ->addData('_links', $expectedSerializedLinks)
            ->shouldBeCalledTimes(1)
        ;

        $jsonHalSerializer = new JsonHalSerializer();
        $jsonHalSerializer->serializeLinks(
            $links,
            $jsonSerializationVisitorProphecy->reveal(),
            $contextProphecy->reveal()
        );
    }

    public function testSerializeCuriesWithMultipleEntriesShouldBeAnArray()
    {
        $links = array(
            new Link('self',   '/users/42'),
            new Link('curies', '/rels/{rel}', array('name' => 'p')),
            new Link('curies', '/foo/rels/{rel}', array('name' => 'foo')),
        );

        $expectedSerializedLinks = array(
            'self' => array(
                'href' => '/users/42',
            ),
            'curies' => array(
                array(
                    'href' => '/rels/{rel}',
                    'name' => 'p',
                ),
                array(
                    'href' => '/foo/rels/{rel}',
                    'name' => 'foo',
                ),
            ),
        );

        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');

        $jsonSerializationVisitorProphecy = $this->prophesize('JMS\Serializer\JsonSerializationVisitor');
        $jsonSerializationVisitorProphecy
            ->addData('_links', $expectedSerializedLinks)
            ->shouldBeCalledTimes(1)
        ;

        $jsonHalSerializer = new JsonHalSerializer();
        $jsonHalSerializer->serializeLinks(
            $links,
            $jsonSerializationVisitorProphecy->reveal(),
            $contextProphecy->reveal()
        );
    }

    public function testSerializeAdrienBrault()
    {
        $hateoas      = HateoasBuilder::buildHateoas();
        $adrienBrault = new AdrienBrault();

        $this
            ->json($hateoas->serialize($adrienBrault, 'json'))
            ->isEqualTo(<<<JSON
{
    "first_name": "Adrien",
    "last_name": "Brault",
    "_links": {
        "self": {
            "href": "http:\/\/adrienbrault.fr"
        },
        "computer": {
            "href": "http:\/\/www.apple.com\/macbook-pro\/"
        },
        "dynamic-relation": {
            "href": "awesome!!!"
        }
    },
    "_embedded": {
        "computer": {
            "name": "MacBook Pro"
        },
        "broken-computer": {
            "name": "Windows Computer"
        }
    }
}
JSON
            );
    }

    public function testSerializeInlineJson()
    {
        $foo1 = new Foo1();
        $foo2 = new Foo2();
        $foo3 = new Foo3();
        $foo1->inline = $foo2;
        $foo2->inline = $foo3;

        $hateoas = HateoasBuilder::buildHateoas();

        $this
            ->json($hateoas->serialize($foo1, 'json'))
            ->isEqualTo(<<<JSON
{
    "_links": {
        "self3": {
            "href": "foo3"
        },
        "self2": {
            "href": "foo2"
        },
        "self1": {
            "href": "foo1"
        }
    },
    "_embedded": {
        "self3": "foo3",
        "self2": "foo2",
        "self1": "foo1"
    }
}
JSON
            );
    }
}

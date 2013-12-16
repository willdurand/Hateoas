<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\Model\Embedded;
use Hateoas\Model\Link;
use Hateoas\Serializer\JsonHalSerializer;
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
}

<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\Model\Embed;
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

        $jsonSerializationVisitorProphecy = $this->prophesize('JMS\Serializer\JsonSerializationVisitor');
        $jsonSerializationVisitorProphecy
            ->addData('_links', $expectedSerializedLinks)
            ->shouldBeCalledTimes(1)
        ;

        $jsonHalSerializer = new JsonHalSerializer();
        $jsonHalSerializer->serializeLinks($links, $jsonSerializationVisitorProphecy->reveal());
    }

    public function testSerializeEmbedded()
    {
        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');
        $contextProphecy
            ->accept(array('name' => 'John'))
            ->willReturnArgument()
        ;

        $embeds = array(
            new Embed('friend', array('name' => 'John')),
        );

        $expectedEmbedded = array(
            'friend' => array('name' => 'John'),
        );

        $jsonSerializationVisitorProphecy = $this->prophesize('JMS\Serializer\JsonSerializationVisitor');
        $jsonSerializationVisitorProphecy
            ->addData('_embedded', $expectedEmbedded)
            ->shouldBeCalledTimes(1)
        ;

        $jsonHalSerializer = new JsonHalSerializer();
        $jsonHalSerializer->serializeEmbedded(
            $embeds,
            $jsonSerializationVisitorProphecy->reveal(),
            $contextProphecy->reveal()
        );
    }
}

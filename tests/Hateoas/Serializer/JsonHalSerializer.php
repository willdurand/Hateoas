<?php

namespace tests\Hateoas\Serializer;

use Hateoas\Configuration\Relation;
use Hateoas\Model\Resource;
use tests\TestCase;
use Hateoas\Model\Link;
use Hateoas\Serializer\JsonHalSerializer as TestedJsonHalSerializer;

class JsonHalSerializer extends TestCase
{
    public function testSerializeLinks()
    {
        $jsonHalSerializer = new TestedJsonHalSerializer();

        $this->mockGenerator->orphanize('__construct');
        $jsonSerializationVisitor = new \mock\JMS\Serializer\JsonSerializationVisitor();

        $links = array(
            new Link('self', '/users/42', array('awesome' => 'exactly')),
            new Link('foo', '/bar'),
            new Link('foo', '/baz'),
            new Link('bar', '/foo'),
            new Link('bar', '/baz'),
            new Link('bar', '/buzz'),
        );

        $jsonHalSerializer->serializeLinks($links, $jsonSerializationVisitor);

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

        $this
            ->mock($jsonSerializationVisitor)
                ->call('addData')
                    ->withArguments('_links', $expectedSerializedLinks)
                    ->once()
        ;
    }

    public function testSerializeEmbedded()
    {
        $jsonHalSerializer = new TestedJsonHalSerializer();

        $this->mockGenerator->orphanize('__construct');
        $jsonSerializationVisitor = new \mock\JMS\Serializer\JsonSerializationVisitor();

        $this->mockGenerator->orphanize('__construct');
        $context = new \mock\JMS\Serializer\SerializationContext();
        $context->getMockController()->accept = function ($data) {
            return $data;
        };

        $embeddedMap = array(
            'friend' => array('name' => 'John'),
        );

        $jsonHalSerializer->serializeEmbedded($embeddedMap, $jsonSerializationVisitor, $context);

        $expectedEmbedded = array(
            'friend' => array('name' => 'John'),
        );

        $this
            ->mock($jsonSerializationVisitor)
                ->call('addData')
                    ->withArguments('_embedded', $expectedEmbedded)
                    ->once()
        ;
    }

    public function testSerializeResource()
    {
        $jsonHalSerializer = new TestedJsonHalSerializer();

        $this->mockGenerator->orphanize('__construct');
        $jsonSerializationVisitor = new \mock\JMS\Serializer\JsonSerializationVisitor();
        $jsonSerializationVisitorClass = new \ReflectionClass('JMS\Serializer\GenericSerializationVisitor');
        $stackProperty = $jsonSerializationVisitorClass->getProperty('dataStack');
        $stackProperty->setAccessible('true');
        $stackProperty->setValue($jsonSerializationVisitor, new \SplStack());

        $this->mockGenerator->orphanize('__construct');
        $context = new \mock\JMS\Serializer\SerializationContext();
        $context->getMockController()->accept = function ($data) {
            return $data;
        };

        $resource = new Resource(array(
            'page' => 2,
            'limit' => 10,
        ), array(
            new Link('self', '/users?page=2'),
            new Link('next', '/users?page=3'),
        ), array(
            'users' => array(
                array('name' => 'Adrien'),
                array('name' => 'William'),
            ),
        ));

        $serializedResource = $jsonHalSerializer->serializeResource($resource, $jsonSerializationVisitor, $context);
        $expectedSerializedResource = array(
            'page' => 2,
            'limit' => 10,
            '_links' => array(
                'self' => array('href' => '/users?page=2'),
                'next' => array('href' => '/users?page=3'),
            ),
            '_embedded' => array(
                'users' => array(
                    array('name' => 'Adrien'),
                    array('name' => 'William'),
                ),
            ),
        );

        $this
            ->array($serializedResource)
                ->isEqualTo($expectedSerializedResource)
        ;
    }
}

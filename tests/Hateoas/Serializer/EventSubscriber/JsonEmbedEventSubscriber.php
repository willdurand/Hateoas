<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use Hateoas\Configuration\Relation;
use tests\TestCase;
use Hateoas\Serializer\EventSubscriber\JsonEmbedEventSubscriber as TestedJsonEmbedEventSubscriber;

class JsonEmbedEventSubscriber extends TestCase
{
    public function testOnPostSerialize()
    {
        $embeddedMap = new \SplObjectStorage();
        $object = new \StdClass();

        $this->mockGenerator->orphanize('__construct');
        $jsonSerializationVisitor = new \mock\JMS\Serializer\JsonSerializationVisitor();

        $jsonSerializer = new \mock\Hateoas\Serializer\JsonSerializerInterface();

        $this->mockGenerator->orphanize('__construct');
        $embeddedMapFactory = new \mock\Hateoas\Factory\EmbeddedMapFactory();
        $embeddedMapFactory->getMockController()->create = function () use ($embeddedMap) {
            return $embeddedMap;
        };

        $this->mockGenerator->orphanize('__construct');
        $context = new \mock\JMS\Serializer\SerializationContext();

        $this->mockGenerator->orphanize('__construct');
        $event = new \mock\JMS\Serializer\EventDispatcher\ObjectEvent();
        $event->getMockController()->getObject = function () use ($object) {
            return $object;
        };
        $event->getMockController()->getVisitor = function () use ($jsonSerializationVisitor) {
            return $jsonSerializationVisitor;
        };
        $event->getMockController()->getContext = function () use ($context) {
            return $context;
        };

        $jsonEmbedSubscriber = new TestedJsonEmbedEventSubscriber($embeddedMapFactory, $jsonSerializer);
        $jsonEmbedSubscriber->onPostSerialize($event);

        $this
            ->mock($embeddedMapFactory)
                ->call('create')
                    ->withArguments($object)
                    ->once()
            ->mock($jsonSerializer)
                ->call('serializeEmbeddedMap')
                    ->withArguments($embeddedMap, $jsonSerializationVisitor, $context)
                    ->once()
        ;
    }
}

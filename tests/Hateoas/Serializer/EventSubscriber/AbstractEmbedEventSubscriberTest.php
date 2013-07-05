<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use tests\TestCase;

abstract class AbstractEmbedEventSubscriberTest extends TestCase
{
    public function testOnPostSerialize()
    {
        $embeddedMap = new \SplObjectStorage();
        $object = new \StdClass();

        $this->mockGenerator->orphanize('__construct');
        $jsonSerializationVisitor = $this->createSerializationVisitorMock();

        $serializer = $this->createSerializerMock();

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

        $embedEventSubscriber = $this->createEmbedEventSubscriber($embeddedMapFactory, $serializer);
        $embedEventSubscriber->onPostSerialize($event);

        $this
            ->mock($embeddedMapFactory)
                ->call('create')
                    ->withArguments($object)
                    ->once()
            ->mock($serializer)
                ->call('serializeEmbedded')
                    ->withArguments($embeddedMap, $jsonSerializationVisitor, $context)
                    ->once()
        ;
    }

    abstract protected function createEmbedEventSubscriber($embeddedMapFactory, $serializer);
    abstract protected function createSerializerMock();
    abstract protected function createSerializationVisitorMock();
}

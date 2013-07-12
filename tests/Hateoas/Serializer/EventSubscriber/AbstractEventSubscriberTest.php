<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use tests\TestCase;

abstract class AbstractEventSubscriberTest extends TestCase
{
    public function testOnPostSerialize()
    {
        $this->mockGenerator->orphanize('__construct');
        $embeds = array(
            new \mock\Hateoas\Model\Embed(),
        );
        $this->mockGenerator->orphanize('__construct');
        $links = array(
            new \mock\Hateoas\Model\Link(),
        );
        $object = new \StdClass();

        $serializationVisitor = $this->createSerializationVisitorMock();

        $serializer = $this->createSerializerMock();

        $this->mockGenerator->orphanize('__construct');
        $linksFactory = new \mock\Hateoas\Factory\LinksFactory();
        $linksFactory->getMockController()->createLinks = function () use ($links) {
            return $links;
        };

        $this->mockGenerator->orphanize('__construct');
        $embedsFactory = new \mock\Hateoas\Factory\EmbedsFactory();
        $embedsFactory->getMockController()->create = function () use ($embeds) {
            return $embeds;
        };

        $this->mockGenerator->orphanize('__construct');
        $context = new \mock\JMS\Serializer\SerializationContext();

        $this->mockGenerator->orphanize('__construct');
        $event = new \mock\JMS\Serializer\EventDispatcher\ObjectEvent();
        $event->getMockController()->getObject = function () use ($object) {
            return $object;
        };
        $event->getMockController()->getVisitor = function () use ($serializationVisitor) {
            return $serializationVisitor;
        };
        $event->getMockController()->getContext = function () use ($context) {
            return $context;
        };

        $embedEventSubscriber = $this->createEventSubscriber($serializer, $linksFactory, $embedsFactory);
        $embedEventSubscriber->onPostSerialize($event);

        $this
            ->mock($embedsFactory)
                ->call('create')
                    ->withArguments($object)
                    ->once()
            ->mock($serializer)
                ->call('serializeEmbedded')
                    ->withArguments($embeds, $serializationVisitor, $context)
                    ->once()
            ->mock($linksFactory)
                ->call('createLinks')
                    ->withArguments($object)
                    ->once()
            ->mock($serializer)
                ->call('serializeLinks')
                    ->withArguments($links, $serializationVisitor)
                    ->once()
        ;
    }

    public function testOnPostSerializeWithNoLinksEmbeds()
    {
        $embeds = array();
        $links = array();
        $object = new \StdClass();

        $serializationVisitor = $this->createSerializationVisitorMock();

        $serializer = $this->createSerializerMock();

        $this->mockGenerator->orphanize('__construct');
        $linksFactory = new \mock\Hateoas\Factory\LinksFactory();
        $linksFactory->getMockController()->createLinks = function () use ($links) {
            return $links;
        };

        $this->mockGenerator->orphanize('__construct');
        $embedsFactory = new \mock\Hateoas\Factory\EmbedsFactory();
        $embedsFactory->getMockController()->create = function () use ($embeds) {
            return $embeds;
        };

        $this->mockGenerator->orphanize('__construct');
        $context = new \mock\JMS\Serializer\SerializationContext();

        $this->mockGenerator->orphanize('__construct');
        $event = new \mock\JMS\Serializer\EventDispatcher\ObjectEvent();
        $event->getMockController()->getObject = function () use ($object) {
            return $object;
        };
        $event->getMockController()->getVisitor = function () use ($serializationVisitor) {
            return $serializationVisitor;
        };
        $event->getMockController()->getContext = function () use ($context) {
            return $context;
        };

        $embedEventSubscriber = $this->createEventSubscriber($serializer, $linksFactory, $embedsFactory);
        $embedEventSubscriber->onPostSerialize($event);

        $this
            ->mock($embedsFactory)
                ->call('create')
                    ->withArguments($object)
                    ->once()
            ->mock($serializer)
                ->call('serializeEmbedded')
                    ->never()
            ->mock($linksFactory)
                ->call('createLinks')
                    ->withArguments($object)
                    ->once()
            ->mock($serializer)
                ->call('serializeLinks')
                    ->never()
        ;
    }

    abstract protected function createEventSubscriber($serializer, $linksFactory, $embeddedMapFactory);
    abstract protected function createSerializerMock();
    abstract protected function createSerializationVisitorMock();
}

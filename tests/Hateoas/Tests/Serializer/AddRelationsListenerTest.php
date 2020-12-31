<?php

declare(strict_types=1);

namespace Hateoas\Tests\Serializer;

use Hateoas\Serializer\AddRelationsListener;
use Hateoas\Tests\TestCase;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AddRelationsListenerTest extends TestCase
{
    use ProphecyTrait;

    protected function createEventSubscriber($serializer, $linksFactory, $embedsFactory)
    {
        $inlineDeferrerProphecy = $this->prophesize('Hateoas\Serializer\Metadata\InlineDeferrer');
        $inlineDeferrerProphecy
            ->handleItems(Argument::cetera())
            ->will(function ($args) {
                return $args[1];
            });

        return new AddRelationsListener(
            $serializer,
            $linksFactory,
            $embedsFactory,
            $inlineDeferrerProphecy->reveal(),
            $inlineDeferrerProphecy->reveal()
        );
    }

    protected function prophesizeSerializer()
    {
        return $this->prophesize('Hateoas\Serializer\SerializerInterface');
    }

    public function testOnPostSerialize()
    {
        $embeddeds = [
            $this->prophesize('Hateoas\Model\Embedded')->reveal(),
        ];
        $links = [
            $this->prophesize('Hateoas\Model\Link')->reveal(),
        ];
        $object = new \StdClass();
        $context = $this->prophesize('JMS\Serializer\SerializationContext')->reveal();

        $serializationVisitor = $this->mockSerializationVisitor();

        $serializerProphecy = $this->prophesizeSerializer();
        $serializerProphecy
            ->serializeEmbeddeds($embeddeds, $serializationVisitor, $context)
            ->shouldBeCalledTimes(1);
        $serializerProphecy
            ->serializeLinks($links, $serializationVisitor, $context)
            ->shouldBeCalledTimes(1);

        $linksFactoryProphecy = $this->prophesize('Hateoas\Factory\LinksFactory');
        $linksFactoryProphecy
            ->create($object, $context)
            ->willReturn($links)
            ->shouldBeCalledTimes(1);

        $embeddedsFactoryProphecy = $this->prophesize('Hateoas\Factory\EmbeddedsFactory');
        $embeddedsFactoryProphecy
            ->create($object, $context)
            ->willReturn($embeddeds)
            ->shouldBeCalledTimes(1);

        $eventProphecy = $this->mockEvent($object, $serializationVisitor, $context);

        $embeddedEventSubscriber = $this->createEventSubscriber(
            $serializerProphecy->reveal(),
            $linksFactoryProphecy->reveal(),
            $embeddedsFactoryProphecy->reveal()
        );
        $embeddedEventSubscriber->onPostSerialize($eventProphecy->reveal());
    }

    public function testOnPostSerializeWithNoLinksEmbeddeds()
    {
        $embeddeds = [];
        $links = [];
        $object = new \StdClass();
        $context = $this->prophesize('JMS\Serializer\SerializationContext')->reveal();

        $serializationVisitor = $this->mockSerializationVisitor();

        $serializerProphecy = $this->prophesizeSerializer();
        $serializerProphecy
            ->serializeEmbeddeds($embeddeds, $serializationVisitor, $context)
            ->shouldNotBeCalled();
        $serializerProphecy
            ->serializeLinks($links, $serializationVisitor)
            ->shouldNotBeCalled();

        $linksFactoryProphecy = $this->prophesize('Hateoas\Factory\LinksFactory');
        $linksFactoryProphecy
            ->create($object, $context)
            ->willReturn($links)
            ->shouldBeCalledTimes(1);

        $embeddedsFactoryProphecy = $this->prophesize('Hateoas\Factory\EmbeddedsFactory');
        $embeddedsFactoryProphecy
            ->create($object, $context)
            ->willReturn($embeddeds)
            ->shouldBeCalledTimes(1);

        $eventProphecy = $this->mockEvent($object, $serializationVisitor, $context);

        $embeddedEventSubscriber = $this->createEventSubscriber(
            $serializerProphecy->reveal(),
            $linksFactoryProphecy->reveal(),
            $embeddedsFactoryProphecy->reveal()
        );
        $embeddedEventSubscriber->onPostSerialize($eventProphecy->reveal());
    }

    private function mockSerializationVisitor()
    {
        return $this->prophesize(SerializationVisitorInterface::class)->reveal();
    }

    private function mockEvent($object, $serializationVisitor, $context)
    {
        $eventProphecy = $this->prophesize('JMS\Serializer\EventDispatcher\ObjectEvent');
        $eventProphecy->getObject()->willreturn($object);
        $eventProphecy->getVisitor()->willreturn($serializationVisitor);
        $eventProphecy->getContext()->willreturn($context);

        return $eventProphecy;
    }
}

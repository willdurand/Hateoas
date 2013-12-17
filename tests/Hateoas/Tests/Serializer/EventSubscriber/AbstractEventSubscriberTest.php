<?php

namespace Hateoas\Tests\Serializer\EventSubscriber;

use Hateoas\Tests\TestCase;

abstract class AbstractEventSubscriberTest extends TestCase
{
    public function testOnPostSerialize()
    {
        $embeddeds = array(
            $this->prophesize('Hateoas\Model\Embedded')->reveal(),
        );
        $links = array(
            $this->prophesize('Hateoas\Model\Link')->reveal(),
        );
        $object = new \StdClass();
        $context = $this->prophesize('JMS\Serializer\SerializationContext')->reveal();

        $serializationVisitor = $this->mockSerializationVisitor();

        $serializerProphecy = $this->prophesizeSerializer();
        $serializerProphecy
            ->serializeEmbeddeds($embeddeds, $serializationVisitor, $context)
            ->shouldBeCalledTimes(1)
        ;
        $serializerProphecy
            ->serializeLinks($links, $serializationVisitor, $context)
            ->shouldBeCalledTimes(1)
        ;

        $linksFactoryProphecy = $this->prophesize('Hateoas\Factory\LinksFactory');
        $linksFactoryProphecy
            ->create($object, $context)
            ->willReturn($links)
            ->shouldBeCalledTimes(1)
        ;

        $embeddedsFactoryProphecy = $this->prophesize('Hateoas\Factory\EmbeddedsFactory');
        $embeddedsFactoryProphecy
            ->create($object, $context)
            ->willReturn($embeddeds)
            ->shouldBeCalledTimes(1)
        ;

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
        $embeddeds = array();
        $links = array();
        $object = new \StdClass();
        $context = $this->prophesize('JMS\Serializer\SerializationContext')->reveal();

        $serializationVisitor = $this->mockSerializationVisitor();

        $serializerProphecy = $this->prophesizeSerializer();
        $serializerProphecy
            ->serializeEmbeddeds($embeddeds, $serializationVisitor, $context)
            ->shouldNotBeCalled()
        ;
        $serializerProphecy
            ->serializeLinks($links, $serializationVisitor)
            ->shouldNotBeCalled()
        ;

        $linksFactoryProphecy = $this->prophesize('Hateoas\Factory\LinksFactory');
        $linksFactoryProphecy
            ->create($object, $context)
            ->willReturn($links)
            ->shouldBeCalledTimes(1)
        ;

        $embeddedsFactoryProphecy = $this->prophesize('Hateoas\Factory\EmbeddedsFactory');
        $embeddedsFactoryProphecy
            ->create($object, $context)
            ->willReturn($embeddeds)
            ->shouldBeCalledTimes(1)
        ;

        $eventProphecy = $this->mockEvent($object, $serializationVisitor, $context);

        $embeddedEventSubscriber = $this->createEventSubscriber(
            $serializerProphecy->reveal(),
            $linksFactoryProphecy->reveal(),
            $embeddedsFactoryProphecy->reveal()
        );
        $embeddedEventSubscriber->onPostSerialize($eventProphecy->reveal());
    }

    abstract protected function createEventSubscriber($serializer, $linksFactory, $embeddedsFactory);

    abstract protected function prophesizeSerializer();

    abstract protected function mockSerializationVisitor();

    private function mockEvent($object, $serializationVisitor, $context)
    {
        $eventProphecy = $this->prophesize('JMS\Serializer\EventDispatcher\ObjectEvent');
        $eventProphecy->getObject()->willreturn($object);
        $eventProphecy->getVisitor()->willreturn($serializationVisitor);
        $eventProphecy->getContext()->willreturn($context);

        return $eventProphecy;
    }
}

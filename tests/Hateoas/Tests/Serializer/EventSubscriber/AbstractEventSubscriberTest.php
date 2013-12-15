<?php

namespace Hateoas\Tests\Serializer\EventSubscriber;

use Hateoas\Tests\TestCase;

abstract class AbstractEventSubscriberTest extends TestCase
{
    public function testOnPostSerialize()
    {
        $embeds = array(
            $this->prophesize('Hateoas\Model\Embed')->reveal(),
        );
        $links = array(
            $this->prophesize('Hateoas\Model\Link')->reveal(),
        );
        $object = new \StdClass();
        $context = $this->prophesize('JMS\Serializer\SerializationContext')->reveal();

        $serializationVisitor = $this->mockSerializationVisitor();

        $serializerProphecy = $this->prophesizeSerializer();
        $serializerProphecy
            ->serializeEmbedded($embeds, $serializationVisitor, $context)
            ->shouldBeCalledTimes(1)
        ;
        $serializerProphecy
            ->serializeLinks($links, $serializationVisitor, $context)
            ->shouldBeCalledTimes(1)
        ;

        $linksFactoryProphecy = $this->prophesize('Hateoas\Factory\LinksFactory');
        $linksFactoryProphecy
            ->createLinks($object, $context)
            ->willReturn($links)
            ->shouldBeCalledTimes(1)
        ;

        $embedsFactoryProphecy = $this->prophesize('Hateoas\Factory\EmbedsFactory');
        $embedsFactoryProphecy
            ->create($object, $context)
            ->willReturn($embeds)
            ->shouldBeCalledTimes(1)
        ;

        $eventProphecy = $this->mockEvent($object, $serializationVisitor, $context);

        $embedEventSubscriber = $this->createEventSubscriber(
            $serializerProphecy->reveal(),
            $linksFactoryProphecy->reveal(),
            $embedsFactoryProphecy->reveal()
        );
        $embedEventSubscriber->onPostSerialize($eventProphecy->reveal());
    }

    public function testOnPostSerializeWithNoLinksEmbeds()
    {
        $embeds = array();
        $links = array();
        $object = new \StdClass();
        $context = $this->prophesize('JMS\Serializer\SerializationContext')->reveal();

        $serializationVisitor = $this->mockSerializationVisitor();

        $serializerProphecy = $this->prophesizeSerializer();
        $serializerProphecy
            ->serializeEmbedded($embeds, $serializationVisitor, $context)
            ->shouldNotBeCalled()
        ;
        $serializerProphecy
            ->serializeLinks($links, $serializationVisitor)
            ->shouldNotBeCalled()
        ;

        $linksFactoryProphecy = $this->prophesize('Hateoas\Factory\LinksFactory');
        $linksFactoryProphecy
            ->createLinks($object, $context)
            ->willReturn($links)
            ->shouldBeCalledTimes(1)
        ;

        $embedsFactoryProphecy = $this->prophesize('Hateoas\Factory\EmbedsFactory');
        $embedsFactoryProphecy
            ->create($object, $context)
            ->willReturn($embeds)
            ->shouldBeCalledTimes(1)
        ;

        $eventProphecy = $this->mockEvent($object, $serializationVisitor, $context);

        $embedEventSubscriber = $this->createEventSubscriber(
            $serializerProphecy->reveal(),
            $linksFactoryProphecy->reveal(),
            $embedsFactoryProphecy->reveal()
        );
        $embedEventSubscriber->onPostSerialize($eventProphecy->reveal());
    }

    abstract protected function createEventSubscriber($serializer, $linksFactory, $embeddedMapFactory);
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

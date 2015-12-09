<?php

namespace Hateoas\Tests\Serializer\EventSubscriber;

use Hateoas\Serializer\EventSubscriber\JsonEventSubscriber;
use Prophecy\Argument;

class JsonEventSubscriberTest extends AbstractEventSubscriberTest
{
    protected function createEventSubscriber($serializer, $linksFactory, $embedsFactory)
    {
        $inlineDeferrerProphecy = $this->prophesize('Hateoas\Serializer\Metadata\InlineDeferrer');
        $inlineDeferrerProphecy
            ->handleItems(Argument::cetera())
            ->will(function ($args) {
                return $args[1];
            })
        ;

        return new JsonEventSubscriber(
            $serializer,
            $linksFactory,
            $embedsFactory,
            $inlineDeferrerProphecy->reveal(),
            $inlineDeferrerProphecy->reveal()
        );
    }

    protected function prophesizeSerializer()
    {
        return $this->prophesize('Hateoas\Serializer\JsonSerializerInterface');
    }

    protected function mockSerializationVisitor()
    {
        return $this->prophesize('JMS\Serializer\JsonSerializationVisitor')->reveal();
    }
}

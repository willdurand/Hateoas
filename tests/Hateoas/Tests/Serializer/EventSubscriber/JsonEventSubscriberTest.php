<?php

namespace Hateoas\Tests\Serializer\EventSubscriber;

use Hateoas\Serializer\EventSubscriber\JsonEventSubscriber;
use Hateoas\Serializer\JsonSerializerRegistry;

class JsonEventSubscriberTest extends AbstractEventSubscriberTest
{
    protected function createEventSubscriber($serializerRegistry, $linksFactory, $embedsFactory)
    {
        $inlineDeferrerProphecy = $this->prophesize('Hateoas\Serializer\Metadata\InlineDeferrer');
        $inlineDeferrerProphecy
            ->handleItems($this->arg->cetera())
            ->will(function ($args) {
                return $args[1];
            })
        ;

        return new JsonEventSubscriber(
            $serializerRegistry,
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

    protected function prophesizeSerializerRegistry()
    {
        return $this->prophesize('Hateoas\Serializer\JsonSerializerRegistry');
    }

    protected function getContextSerializerNameGetterName()
    {
        return 'getJsonSerializerName';
    }
}

<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use Hateoas\Serializer\EventSubscriber\JsonEventSubscriber as TestedJsonEventSubscriber;

class JsonEventSubscriber extends AbstractEventSubscriberTest
{
    protected function createEventSubscriber($serializer, $linksFactory, $embedsFactory)
    {
        $this->mockGenerator->orphanize('__construct');
        $inlineDeferrer = new \mock\Hateoas\Serializer\Metadata\InlineDeferrer();
        $inlineDeferrer->getMockController()->handleItems = function ($object, $items) {
            return $items;
        };

        return new TestedJsonEventSubscriber(
            $serializer,
            $linksFactory,
            $embedsFactory,
            $inlineDeferrer,
            $inlineDeferrer
        );
    }

    protected function createSerializerMock()
    {
        return new \mock\Hateoas\Serializer\JsonSerializerInterface();
    }

    protected function createSerializationVisitorMock()
    {
        $this->mockGenerator->orphanize('__construct');

        return new \mock\JMS\Serializer\JsonSerializationVisitor();
    }
}

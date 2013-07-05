<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use Hateoas\Serializer\EventSubscriber\JsonEmbedEventSubscriber as TestedJsonEmbedEventSubscriber;

class JsonEmbedEventSubscriber extends AbstractEmbedEventSubscriberTest
{
    protected function createEmbedEventSubscriber($embeddedMapFactory, $serializer)
    {
        return new TestedJsonEmbedEventSubscriber($embeddedMapFactory, $serializer);
    }

    protected function createSerializerMock()
    {
        return new \mock\Hateoas\Serializer\JsonSerializerInterface();
    }

    protected function createSerializationVisitorMock()
    {
        return new \mock\JMS\Serializer\JsonSerializationVisitor();
    }
}

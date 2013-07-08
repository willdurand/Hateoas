<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use Hateoas\Serializer\EventSubscriber\JsonLinkEventSubscriber as TestedJsonLinkEventSubscriber;

class JsonLinkEventSubscriber extends AbstractLinkEventSubscriberTest
{
    protected function createLinkEventSubscriber($linksFactory, $serializer)
    {
        return new TestedJsonLinkEventSubscriber($linksFactory, $serializer);
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

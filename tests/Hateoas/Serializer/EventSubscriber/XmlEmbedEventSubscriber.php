<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use Hateoas\Serializer\EventSubscriber\XmlEmbedEventSubscriber as TestedXmlEmbedEventSubscriber;

class XmlEmbedEventSubscriber extends AbstractEmbedEventSubscriberTest
{
    protected function createEmbedEventSubscriber($embeddedMapFactory, $serializer)
    {
        return new TestedXmlEmbedEventSubscriber($embeddedMapFactory, $serializer);
    }

    protected function createSerializerMock()
    {
        return new \mock\Hateoas\Serializer\XmlSerializerInterface();
    }

    protected function createSerializationVisitorMock()
    {
        return new \mock\JMS\Serializer\XmlSerializationVisitor();
    }
}

<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use Hateoas\Serializer\EventSubscriber\XmlEventSubscriber as TestedXmlEventSubscriber;

class XmlEventSubscriber extends AbstractEventSubscriberTest
{
    protected function createEventSubscriber($serializer, $linksFactory, $embedsFactory)
    {
        return new TestedXmlEventSubscriber($serializer, $linksFactory, $embedsFactory);
    }

    protected function createSerializerMock()
    {
        return new \mock\Hateoas\Serializer\XmlSerializerInterface();
    }

    protected function createSerializationVisitorMock()
    {
        $this->mockGenerator->orphanize('__construct');

        return new \mock\JMS\Serializer\XmlSerializationVisitor();
    }
}

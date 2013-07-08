<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use Hateoas\Serializer\EventSubscriber\XmlLinkEventSubscriber as TestedXmlLinkEventSubscriber;

class XmlLinkEventSubscriber extends AbstractLinkEventSubscriberTest
{
    protected function createLinkEventSubscriber($linksFactory, $serializer)
    {
        return new TestedXmlLinkEventSubscriber($linksFactory, $serializer);
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

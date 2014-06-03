<?php

namespace Hateoas\Tests\Serializer\EventSubscriber;

use Hateoas\Serializer\EventSubscriber\XmlEventSubscriber;
use Hateoas\Serializer\XmlSerializerRegistry;

class XmlEventSubscriberTest extends AbstractEventSubscriberTest
{
    protected function createEventSubscriber($serializerRegistry, $linksFactory, $embedsFactory)
    {
        return new XmlEventSubscriber($serializerRegistry, $linksFactory, $embedsFactory);
    }

    protected function prophesizeSerializer()
    {
        return $this->prophesize('Hateoas\Serializer\XmlSerializerInterface');
    }

    protected function mockSerializationVisitor()
    {
        return $this->prophesize('JMS\Serializer\XmlSerializationVisitor')->reveal();
    }

    protected function prophesizeSerializerRegistry()
    {
        return $this->prophesize('Hateoas\Serializer\XmlSerializerRegistry');
    }

    protected function getContextSerializerNameGetterName()
    {
        return 'getXmlSerializerName';
    }
}

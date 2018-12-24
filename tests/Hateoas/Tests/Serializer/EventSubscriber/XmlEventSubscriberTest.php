<?php

declare(strict_types=1);

namespace Hateoas\Tests\Serializer\EventSubscriber;

use Hateoas\Serializer\EventSubscriber\XmlEventSubscriber;

class XmlEventSubscriberTest extends AbstractEventSubscriberTest
{
    protected function createEventSubscriber($serializer, $linksFactory, $embedsFactory)
    {
        return new XmlEventSubscriber($serializer, $linksFactory, $embedsFactory);
    }

    protected function prophesizeSerializer()
    {
        return $this->prophesize('Hateoas\Serializer\SerializerInterface');
    }
}

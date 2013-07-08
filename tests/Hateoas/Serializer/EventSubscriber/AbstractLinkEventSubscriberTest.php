<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use tests\TestCase;

abstract class AbstractLinkEventSubscriberTest extends TestCase
{
    public function testOnPostSerialize()
    {
        $this->mockGenerator->orphanize('__construct');

        $links = array(
            new \mock\Hateoas\Model\Link(),
        );
        $object = new \StdClass();

        $this->mockGenerator->orphanize('__construct');
        $linksFactory = new \mock\Hateoas\Factory\LinksFactory();
        $linksFactory->getMockController()->createLinks = function () use ($links) {
            return $links;
        };
        $serializer = $this->createSerializerMock();

        $this->mockGenerator->orphanize('__construct');
        $serializationVisitor = $this->createSerializationVisitorMock();

        $this->mockGenerator->orphanize('__construct');
        $event = new \mock\JMS\Serializer\EventDispatcher\ObjectEvent();
        $event->getMockController()->getObject = function () use ($object) {
            return $object;
        };
        $event->getMockController()->getVisitor = function () use ($serializationVisitor) {
            return $serializationVisitor;
        };

        $linkEventSubscriber = $this->createLinkEventSubscriber($linksFactory, $serializer);
        $linkEventSubscriber->onPostSerialize($event);

        $this
            ->mock($linksFactory)
                ->call('createLinks')
                    ->withArguments($object)
                    ->once()
            ->mock($serializer)
                ->call('serializeLinks')
                    ->withArguments($links, $serializationVisitor)
                    ->once()
        ;
    }

    abstract protected function createLinkEventSubscriber($linksFactory, $serializer);
    abstract protected function createSerializerMock();
    abstract protected function createSerializationVisitorMock();
}

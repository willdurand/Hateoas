<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use tests\TestCase;
use Hateoas\Serializer\EventSubscriber\JsonLinkEventSubscriber as TestedJsonLinkEventSubscriber;

class JsonLinkEventSubscriber extends TestCase
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
        $jsonSerializer = new \mock\Hateoas\Serializer\JsonSerializerInterface();

        $this->mockGenerator->orphanize('__construct');
        $jsonSerializationVisitor = new \mock\JMS\Serializer\JsonSerializationVisitor();

        $this->mockGenerator->orphanize('__construct');
        $event = new \mock\JMS\Serializer\EventDispatcher\ObjectEvent();
        $event->getMockController()->getObject = function () use ($object) {
            return $object;
        };
        $event->getMockController()->getVisitor = function () use ($jsonSerializationVisitor) {
            return $jsonSerializationVisitor;
        };

        $jsonLinkEventSubscriber = new TestedJsonLinkEventSubscriber($linksFactory, $jsonSerializer);
        $jsonLinkEventSubscriber->onPostSerialize($event);

        $this
            ->mock($linksFactory)
                ->call('createLinks')
                    ->withArguments($object)
                    ->once()
            ->mock($jsonSerializer)
                ->call('serializeLinks')
                    ->withArguments($links, $jsonSerializationVisitor)
                    ->once()
        ;
    }
}

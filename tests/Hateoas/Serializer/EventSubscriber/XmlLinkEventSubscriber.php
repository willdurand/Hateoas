<?php

namespace tests\Hateoas\Serializer\EventSubscriber;

use tests\TestCase;
use Hateoas\Serializer\EventSubscriber\XmlLinkEventSubscriber as TestedXmlLinkEventSubscriber;

class XmlLinkEventSubscriber extends TestCase
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
        $xmlSerializer = new \mock\Hateoas\Serializer\XmlSerializerInterface();

        $this->mockGenerator->orphanize('__construct');
        $xmlSerializationVisitor = new \mock\JMS\Serializer\XmlSerializationVisitor();

        $this->mockGenerator->orphanize('__construct');
        $event = new \mock\JMS\Serializer\EventDispatcher\ObjectEvent();
        $event->getMockController()->getObject = function () use ($object) {
            return $object;
        };
        $event->getMockController()->getVisitor = function () use ($xmlSerializationVisitor) {
            return $xmlSerializationVisitor;
        };

        $xmlLinkEventSubscriber = new TestedXmlLinkEventSubscriber($linksFactory, $xmlSerializer);
        $xmlLinkEventSubscriber->onPostSerialize($event);

        $this
            ->mock($linksFactory)
                ->call('createLinks')
                    ->withArguments($object)
                    ->once()
            ->mock($xmlSerializer)
                ->call('serializeLinks')
                    ->withArguments($links, $xmlSerializationVisitor)
                    ->once()
        ;
    }
}

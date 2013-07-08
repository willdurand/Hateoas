<?php

namespace tests\Hateoas\Serializer\Handler;

use tests\TestCase;
use Hateoas\Serializer\Handler\XmlResourceHandler as TestedXmlResourceHandler;

class XmlResourceHandler extends TestCase
{
    public function testSerializeToXml()
    {
        $this->mockGenerator->orphanize('__construct');
        $serializer = new \mock\Hateoas\Serializer\XmlSerializerInterface();

        $this->mockGenerator->orphanize('__construct');
        $resource = new \mock\Hateoas\Model\Resource();

        $this->mockGenerator->orphanize('__construct');
        $visitor = new \mock\JMS\Serializer\XmlSerializationVisitor();

        $this->mockGenerator->orphanize('__construct');
        $context = new \mock\JMS\Serializer\SerializationContext();

        $handler = new TestedXmlResourceHandler($serializer);

        $handler->serializeToXml($visitor, $resource, array(), $context);

        $this
            ->mock($serializer)
                ->call('serializeResource')
                    ->withArguments($resource, $visitor, $context)
                    ->once()
        ;
    }
}

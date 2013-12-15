<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\Tests\TestCase;
use Hateoas\Model\Link;
use Hateoas\Serializer\XmlSerializer;
use JMS\Serializer\XmlSerializationVisitor;

class XmlSerializerTest extends TestCase
{
    public function testSerializeLinks()
    {
        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');

        $xmlSerializer = new XmlSerializer();
        $xmlSerializationVisitor = $this->createXmlSerializationVisitor();

        $links = array(
            new Link('self', '/users/42'),
            new Link('foo', '/bar', array('type' => 'magic')),
        );

        $xmlSerializer->serializeLinks(
            $links,
            $xmlSerializationVisitor,
            $contextProphecy->reveal()
        );

        $this
            ->string($xmlSerializationVisitor->getResult())
                ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
  <link rel="self" href="/users/42"/>
  <link rel="foo" href="/bar" type="magic"/>
</root>

XML
                )
        ;
    }

    public function testSerializeEmbeds()
    {
        // TODO ... seemed hard to test :(
    }

    private function createXmlSerializationVisitor()
    {
        $xmlSerializationVisitor = new XmlSerializationVisitor(
            $this->prophesize('JMS\Serializer\Naming\PropertyNamingStrategyInterface')->reveal()
        );
        $xmlSerializationVisitorClass = new \ReflectionClass('JMS\Serializer\XmlSerializationVisitor');
        $stackProperty = $xmlSerializationVisitorClass->getProperty('stack');
        $stackProperty->setAccessible('true');
        $stackProperty->setValue($xmlSerializationVisitor, new \SplStack());

        $xmlSerializationVisitor->document = $xmlSerializationVisitor->createDocument(null, null, false);
        $xmlRootNode = $xmlSerializationVisitor->document->createElement('root');
        $xmlSerializationVisitor->document->appendChild($xmlRootNode);
        $xmlSerializationVisitor->setCurrentNode($xmlRootNode);

        return $xmlSerializationVisitor;
    }
}

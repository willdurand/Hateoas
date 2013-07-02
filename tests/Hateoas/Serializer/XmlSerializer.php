<?php

namespace tests\Hateoas\Serializer;

use tests\Test;
use Hateoas\Model\Link;
use Hateoas\Serializer\XmlSerializer as TestedXmlSerializer;

class XmlSerializer extends Test
{
    public function testSerializeLinks()
    {
        $xmlSerializer = new TestedXmlSerializer();
        $xmlSerializationVisitor = $this->createXmlSerializationVisitor();

        $links = array(
            new Link('self', '/users/42'),
            new Link('foo', '/bar', array('type' => 'magic')),
        );

        $xmlSerializer->serializeLinks($links, $xmlSerializationVisitor);

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

    private function createXmlSerializationVisitor()
    {
        $xmlSerializationVisitor = new \mock\JMS\Serializer\XmlSerializationVisitor(
            new \mock\JMS\Serializer\Naming\PropertyNamingStrategyInterface()
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

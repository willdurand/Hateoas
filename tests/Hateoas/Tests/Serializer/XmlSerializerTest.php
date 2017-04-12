<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\HateoasBuilder;
use Hateoas\Model\Embedded;
use Hateoas\Model\Link;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use Hateoas\Serializer\XmlSerializer;
use Hateoas\Tests\Fixtures\AdrienBrault;
use Hateoas\Tests\Fixtures\Gh236Foo;
use Hateoas\Tests\TestCase;
use JMS\Serializer\SerializationContext;
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

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
  <link rel="self" href="/users/42"/>
  <link rel="foo" href="/bar" type="magic"/>
</root>

XML
            ,
            $xmlSerializationVisitor->getResult()
        );
    }

    public function testSerializeEmbeddeds()
    {
        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');

        $embeddeds = array(
            new Embedded('friend', array('name' => 'John'), new RelationPropertyMetadata(), 'person'),
        );

        $xmlSerializationVisitor = $this->createXmlSerializationVisitor();

        $xmlSerializer = new XmlSerializer();
        $xmlSerializer->serializeEmbeddeds(
            $embeddeds,
            $xmlSerializationVisitor,
            $contextProphecy->reveal()
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
  <person rel="friend">
    <entry/>
  </person>
</root>

XML
            ,
            $xmlSerializationVisitor->getResult()
        );
    }

    public function testSerializeAdrienBrault()
    {
        $hateoas      = HateoasBuilder::buildHateoas();
        $adrienBrault = new AdrienBrault();

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <first_name><![CDATA[Adrien]]></first_name>
  <last_name><![CDATA[Brault]]></last_name>
  <link rel="self" href="http://adrienbrault.fr"/>
  <link rel="computer" href="http://www.apple.com/macbook-pro/"/>
  <link rel="dynamic-relation" href="awesome!!!"/>
  <computer rel="computer">
    <name><![CDATA[MacBook Pro]]></name>
  </computer>
  <computer rel="broken-computer">
    <name><![CDATA[Windows Computer]]></name>
  </computer>
  <smartphone rel="smartphone">
    <name><![CDATA[iPhone 6]]></name>
  </smartphone>
  <smartphone rel="smartphone">
    <name><![CDATA[Nexus 5]]></name>
  </smartphone>
  <entry rel="dynamic-relation">
    <entry><![CDATA[wowowow]]></entry>
  </entry>
</result>

XML
            ,
            $hateoas->serialize($adrienBrault, 'xml')
        );
    }

    public function testGh236()
    {
        $data = new CollectionRepresentation([new Gh236Foo()]);

        $hateoas = HateoasBuilder::buildHateoas();

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <entry rel="items">
    <entry>
      <a>
        <xxx><![CDATA[yyy]]></xxx>
      </a>
      <entry rel="b_embed">
        <xxx><![CDATA[zzz]]></xxx>
      </entry>
    </entry>
  </entry>
</collection>

XML
            ,
            $hateoas->serialize($data, 'xml', SerializationContext::create()->enableMaxDepthChecks())
        );
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

<?php

declare(strict_types=1);

namespace Hateoas\Tests\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Hateoas\HateoasBuilder;
use Hateoas\Model\Embedded;
use Hateoas\Model\Link;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use Hateoas\Serializer\XmlSerializer;
use Hateoas\Tests\Fixtures\AdrienBrault;
use Hateoas\Tests\Fixtures\Attribute;
use Hateoas\Tests\Fixtures\Gh236Foo;
use Hateoas\Tests\Fixtures\LinkAttributes;
use Hateoas\Tests\TestCase;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlSerializationVisitor;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class XmlSerializerTest extends TestCase
{
    use ProphecyTrait;

    public function testSerializeLinks()
    {
        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');

        $xmlSerializer = new XmlSerializer();
        $xmlSerializationVisitor = $this->createXmlSerializationVisitor();

        $links = [
            new Link('self', '/users/42'),
            new Link('foo', '/bar', ['type' => 'magic']),
        ];

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
            $xmlSerializationVisitor->getResult($xmlSerializationVisitor->getDocument())
        );
    }

    public function testSerializeEmbeddeds()
    {
        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');
        $navigatorProphecy = $this->prophesize('JMS\Serializer\GraphNavigatorInterface');

        $contextProphecy
            ->getNavigator()
            ->willReturn($navigatorProphecy);

        $contextProphecy->pushPropertyMetadata(Argument::type('Hateoas\Serializer\Metadata\RelationPropertyMetadata'))->shouldBeCalled();
        $contextProphecy->popPropertyMetadata()->shouldBeCalled();

        $embeddeds = [
            new Embedded('friend', ['name' => 'John'], new RelationPropertyMetadata(), 'person'),
        ];

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
            $xmlSerializationVisitor->getResult($xmlSerializationVisitor->getCurrentNode())
        );
    }

    public function testSerializeAdrienBrault()
    {
        $hateoas      = HateoasBuilder::buildHateoas();
        if (class_exists(AnnotationReader::class)) {
            $adrienBrault = new AdrienBrault();
        } else {
            $adrienBrault = new Attribute\AdrienBrault();
        }

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
        if (class_exists(AnnotationReader::class)) {
            $data = new CollectionRepresentation([new Gh236Foo()]);
        } else {
            $data = new CollectionRepresentation([new Attribute\Gh236Foo()]);
        }

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
        $xmlSerializationVisitor = new XmlSerializationVisitor();
        $xmlSerializationVisitorClass = new \ReflectionClass('JMS\Serializer\XmlSerializationVisitor');
        $stackProperty = $xmlSerializationVisitorClass->getProperty('stack');
        $stackProperty->setAccessible(true);
        $stackProperty->setValue($xmlSerializationVisitor, new \SplStack());

        $xmlRootNode = $document = $xmlSerializationVisitor->createRoot(null, 'root');
        $xmlSerializationVisitor->setCurrentNode($xmlRootNode);

        return $xmlSerializationVisitor;
    }

    public function testTemplateLink()
    {
        $data = new LinkAttributes();

        $hateoas = HateoasBuilder::create()
            ->setXmlSerializer(new XmlSerializer())
            ->addMetadataDir(__DIR__ . '/../Fixtures/config/')
            ->build();

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <link rel="self" href="https://github.com/willdurand/Hateoas/issues/305" templated="false"/>
  <link rel="foo" href="http://foo{?bar}" templated="true"/>
  <link rel="bar" href="http://foo/bar" templated="false" number="2"/>
</result>

XML
            ,
            $hateoas->serialize($data, 'xml')
        );
    }
}

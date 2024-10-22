<?php

declare(strict_types=1);

namespace Hateoas\Tests\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Hateoas\HateoasBuilder;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Serializer\XmlHalSerializer;
use Hateoas\Tests\Fixtures\AdrienBrault;
use Hateoas\Tests\Fixtures\Attribute;
use Hateoas\Tests\Fixtures\Gh236Foo;
use Hateoas\Tests\Fixtures\LinkAttributes;
use Hateoas\Tests\TestCase;
use JMS\Serializer\SerializationContext;

class XmlHalSerializerTest extends TestCase
{
    public function testSerializeAdrienBrault()
    {
        $hateoas = HateoasBuilder::create()
            ->setXmlSerializer(new XmlHalSerializer())
            ->build();
        if (class_exists(AnnotationReader::class)) {
            $adrienBrault = new AdrienBrault();
        } else {
            $adrienBrault = new Attribute\AdrienBrault();
        }

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result href="http://adrienbrault.fr">
  <first_name><![CDATA[Adrien]]></first_name>
  <last_name><![CDATA[Brault]]></last_name>
  <link rel="computer" href="http://www.apple.com/macbook-pro/"/>
  <link rel="dynamic-relation" href="awesome!!!"/>
  <resource rel="computer">
    <name><![CDATA[MacBook Pro]]></name>
  </resource>
  <resource rel="broken-computer">
    <name><![CDATA[Windows Computer]]></name>
  </resource>
  <resource rel="smartphone">
    <name><![CDATA[iPhone 6]]></name>
  </resource>
  <resource rel="smartphone">
    <name><![CDATA[Nexus 5]]></name>
  </resource>
  <resource rel="dynamic-relation"><![CDATA[wowowow]]></resource>
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

        $hateoas = HateoasBuilder::create()
            ->setXmlSerializer(new XmlHalSerializer())
            ->build();

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <resource rel="items">
    <a>
      <xxx><![CDATA[yyy]]></xxx>
    </a>
    <resource rel="b_embed">
      <xxx><![CDATA[zzz]]></xxx>
    </resource>
  </resource>
</collection>

XML
            ,
            $hateoas->serialize($data, 'xml', SerializationContext::create()->enableMaxDepthChecks())
        );
    }

    public function testTemplateLink()
    {
        $data = new LinkAttributes();

        $hateoas = HateoasBuilder::create()
            ->setXmlSerializer(new XmlHalSerializer())
            ->addMetadataDir(__DIR__ . '/../Fixtures/config/')
            ->build();

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result templated="false" href="https://github.com/willdurand/Hateoas/issues/305">
  <link rel="foo" href="http://foo{?bar}" templated="true"/>
  <link rel="bar" href="http://foo/bar" templated="false" number="2"/>
</result>

XML
            ,
            $hateoas->serialize($data, 'xml')
        );
    }
}

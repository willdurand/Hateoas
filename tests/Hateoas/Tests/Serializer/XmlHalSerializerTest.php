<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\HateoasBuilder;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Serializer\XmlHalSerializer;
use Hateoas\Tests\Fixtures\AdrienBrault;
use Hateoas\Tests\Fixtures\Gh236Foo;
use Hateoas\Tests\TestCase;
use JMS\Serializer\SerializationContext;

class XmlHalSerializerTest extends TestCase
{
    public function testSerializeAdrienBrault()
    {
        $hateoas = HateoasBuilder::create()
            ->setXmlSerializer(new XmlHalSerializer())
            ->build();
        $adrienBrault = new AdrienBrault();

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
        $data = new CollectionRepresentation([new Gh236Foo()]);

        $hateoas = HateoasBuilder::create()
            ->setXmlSerializer(new XmlHalSerializer())
            ->build();

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <resource rel="items">
    <bar>
      <xxx><![CDATA[yyy]]></xxx>
    </bar>
  </resource>
</collection>

XML
            ,
            $hateoas->serialize($data, 'xml', SerializationContext::create()->enableMaxDepthChecks())
        );
    }
}

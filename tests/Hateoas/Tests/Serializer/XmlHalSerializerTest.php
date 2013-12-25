<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\HateoasBuilder;
use Hateoas\Serializer\XmlHalSerializer;
use Hateoas\Tests\Fixtures\AdrienBrault;
use Hateoas\Tests\TestCase;

class XmlHalSerializerTest extends TestCase
{
    public function testSerializeAdrienBrault()
    {
        $hateoas = HateoasBuilder::create()
            ->setXmlSerializer(new XmlHalSerializer())
            ->build();
        $adrienBrault = new AdrienBrault();

        $this
            ->string($hateoas->serialize($adrienBrault, 'xml'))
            ->isEqualTo(<<<XML
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
</result>

XML
            );
    }
}

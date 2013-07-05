<?php

namespace tests\Hateoas;

use tests\fixtures\AdrienBrault;
use tests\TestCase;
use Hateoas\HateoasBuilder as TestedHateoasBuilder;

class HateoasBuilder extends TestCase
{
    public function test()
    {
        $hateoasBuilder = new TestedHateoasBuilder();
        $hateoas = $hateoasBuilder->build();

        $this
            ->object($hateoas)
                ->isInstanceOf('Hateoas\Hateoas')
        ;
    }

    public function testSerializeAdrienBrault()
    {
        $hateoas = TestedHateoasBuilder::buildHateoas();
        $adrienBrault = new AdrienBrault();

        $this
            ->string($hateoas->serialize($adrienBrault, 'xml'))
                ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <first_name><![CDATA[Adrien]]></first_name>
  <last_name><![CDATA[Brault]]></last_name>
  <link rel="self" href="http://adrienbrault.fr"/>
  <link rel="computer" href="http://www.apple.com/macbook-pro/"/>
  <entry rel="computer">
    <entry><![CDATA[MacBook Pro]]></entry>
  </entry>
</result>

XML
                )
            ->string($hateoas->serialize($adrienBrault, 'json'))
                ->isEqualTo(<<<JSON
{"first_name":"Adrien","last_name":"Brault","_links":{"self":{"href":"http:\/\/adrienbrault.fr"},"computer":{"href":"http:\/\/www.apple.com\/macbook-pro\/"}},"_embedded":{"computer":{"name":"MacBook Pro"}}}
JSON
                )

        ;
    }
}

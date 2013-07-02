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
        $serializerBuilder = $hateoasBuilder->configureSerializerBuilder();

        $this
            ->object($serializerBuilder)
                ->isInstanceOf('JMS\Serializer\SerializerBuilder')
        ;
    }

    public function testSerializeAdrienBrault()
    {
        $serializer = TestedHateoasBuilder::getSerializer();
        $adrienBrault = new AdrienBrault();

        $this
            ->string($serializer->serialize($adrienBrault, 'xml'))
                ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <first_name><![CDATA[Adrien]]></first_name>
  <last_name><![CDATA[Brault]]></last_name>
  <link rel="self" href="http://adrienbrault.fr"/>
</result>

XML
                )
        ;
    }
}

<?php

namespace Hateoas\Tests;

use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use Hateoas\Serializer\XmlHalSerializer;
use JMS\Serializer\SerializationContext;
use Hateoas\Tests\Fixtures\AdrienBrault;
use Hateoas\Tests\Fixtures\Foo1;
use Hateoas\Tests\Fixtures\Foo2;
use Hateoas\Tests\Fixtures\Foo3;
use Hateoas\Tests\Fixtures\WithAlternativeRouter;
use Hateoas\Tests\TestCase;

/**
 * Contains functional tests
 */
class HateoasBuilderTest extends TestCase
{
    public function test()
    {
        $hateoasBuilder = new HateoasBuilder();
        $hateoas = $hateoasBuilder->build();

        $this
            ->object($hateoas)
                ->isInstanceOf('Hateoas\Hateoas')
        ;
    }

    public function testSerializeAdrienBrault()
    {
        $hateoas = HateoasBuilder::buildHateoas();
        $halHateoas = HateoasBuilder::create()
            ->setXmlSerializer(new XmlHalSerializer())
            ->build();
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
  <link rel="dynamic-relation" href="awesome!!!"/>
  <computer rel="computer">
    <name><![CDATA[MacBook Pro]]></name>
  </computer>
  <computer rel="broken-computer">
    <name><![CDATA[Windows Computer]]></name>
  </computer>
</result>

XML
                )
            ->string($halHateoas->serialize($adrienBrault, 'xml'))
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
                )
            ->string($hateoas->serialize($adrienBrault, 'json'))
                ->isEqualTo(<<<JSON
{"first_name":"Adrien","last_name":"Brault","_links":{"self":{"href":"http:\/\/adrienbrault.fr"},"computer":{"href":"http:\/\/www.apple.com\/macbook-pro\/"},"dynamic-relation":{"href":"awesome!!!"}},"_embedded":{"computer":{"name":"MacBook Pro"},"broken-computer":{"name":"Windows Computer"}}}
JSON
                )

        ;
    }

    public function testSerializeAdrienBraultWithExclusion()
    {
        $hateoas = HateoasBuilder::buildHateoas();
        $adrienBrault = new AdrienBrault();
        $fakeAdrienBrault = new AdrienBrault();
        $fakeAdrienBrault->firstName = 'John';
        $fakeAdrienBrault->lastName = 'Smith';
        $context = SerializationContext::create()->setGroups(array('simple'));
        $context2 = clone $context;

        $this
            ->string($hateoas->serialize($adrienBrault, 'xml', $context))
                ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <first_name><![CDATA[Adrien]]></first_name>
  <last_name><![CDATA[Brault]]></last_name>
  <link rel="self" href="http://adrienbrault.fr"/>
  <link rel="computer" href="http://www.apple.com/macbook-pro/"/>
</result>

XML
                )
            ->string($hateoas->serialize($fakeAdrienBrault, 'xml', $context2))
                ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <first_name><![CDATA[John]]></first_name>
  <last_name><![CDATA[Smith]]></last_name>
  <link rel="computer" href="http://www.apple.com/macbook-pro/"/>
</result>

XML
                )
        ;
    }

    public function testAlternativeUrlGenerator()
    {
        $brokenUrlGenerator = new CallableUrlGenerator(function ($name, $parameters) {
            return $name . '?' . http_build_query($parameters);
        });

        $hateoas = HateoasBuilder::create()
            ->setUrlGenerator('my_generator', $brokenUrlGenerator)
            ->build()
        ;

        $this
            ->string($hateoas->serialize(new WithAlternativeRouter(), 'xml'))
            ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <link rel="search" href="/search?query=hello"/>
</result>

XML
            )
        ;
    }

    public function testSerializeInlineJson()
    {
        $foo1 = new Foo1();
        $foo2 = new Foo2();
        $foo3 = new Foo3();
        $foo1->inline = $foo2;
        $foo2->inline = $foo3;

        $hateoas = HateoasBuilder::buildHateoas();

        $this
            ->string($hateoas->serialize($foo1, 'json'))
                ->isEqualTo(
                    '{'.
                        '"_links":{'.
                            '"self3":{"href":"foo3"},'.
                            '"self2":{"href":"foo2"},'.
                            '"self1":{"href":"foo1"}},'.
                        '"_embedded":{'.
                            '"self3":"foo3",'.
                            '"self2":"foo2",'.
                            '"self1":"foo1"'.
                        '}'.
                    '}'
                )
        ;
    }
}

<?php

namespace tests\Hateoas;

use Hateoas\Model\Link;
use Hateoas\Model\Resource;
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

    public function testSerializeResource()
    {
        $resource = new Resource(array(
            'page' => 2,
            'limit' => 10,
        ), array(
            new Link('self', '/users?page=2'),
            new Link('next', '/users?page=3'),
        ), array(
            'users' => array(
                'Adrien',
                'William',
            ),
        ));

        $hateoas = TestedHateoasBuilder::buildHateoas();

        $this
            ->string($hateoas->serialize($resource, 'xml'))
                ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <page>2</page>
  <limit>10</limit>
  <link rel="self" href="/users?page=2"/>
  <link rel="next" href="/users?page=3"/>
  <entry rel="users">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </entry>
</result>

XML
                )
            ->string($hateoas->serialize($resource, 'json'))
                ->isEqualTo(<<<JSON
{"page":2,"limit":10,"_links":{"self":{"href":"\/users?page=2"},"next":{"href":"\/users?page=3"}},"_embedded":{"users":["Adrien","William"]}}
JSON
                )
        ;
    }
}

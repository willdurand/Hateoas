<?php

namespace tests\Hateoas;

use Hateoas\Model\Embed;
use Hateoas\Model\Link;
use Hateoas\Model\Resource;
use Hateoas\HateoasBuilder as TestedHateoasBuilder;
use tests\fixtures\AdrienBrault;
use tests\fixtures\SimpleUser;
use tests\TestCase;

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
        $halHateoas = TestedHateoasBuilder::create()
            ->addXmlHalSerializer()
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
  <computer rel="computer">
    <name><![CDATA[MacBook Pro]]></name>
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
  <resource rel="computer">
    <name><![CDATA[MacBook Pro]]></name>
  </resource>
</result>

XML
                )
            ->string($hateoas->serialize($adrienBrault, 'json'))
                ->isEqualTo(<<<JSON
{"first_name":"Adrien","last_name":"Brault","_links":{"self":{"href":"http:\/\/adrienbrault.fr"},"computer":{"href":"http:\/\/www.apple.com\/macbook-pro\/"}},"_embedded":{"computer":{"name":"MacBook Pro","_links":[],"_embedded":[]}}}
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
            new Embed('user', array(
                'Adrien',
                'William',
            ), 'users'),
            new Embed('test', 'test'),
        ), 'users');

        $hateoas = TestedHateoasBuilder::buildHateoas();
        $halHateoas = TestedHateoasBuilder::create()
            ->addXmlHalSerializer()
            ->build();

        $this
            ->string($hateoas->serialize($resource, 'xml'))
                ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<users>
  <page>2</page>
  <limit>10</limit>
  <link rel="self" href="/users?page=2"/>
  <link rel="next" href="/users?page=3"/>
  <users rel="user">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
  <entry rel="test"><![CDATA[test]]></entry>
</users>

XML
                )
            ->string($halHateoas->serialize($resource, 'xml'))
                ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<resource href="/users?page=2">
  <page>2</page>
  <limit>10</limit>
  <link rel="next" href="/users?page=3"/>
  <resource rel="user"><![CDATA[Adrien]]></resource>
  <resource rel="user"><![CDATA[William]]></resource>
  <resource rel="test"><![CDATA[test]]></resource>
</resource>

XML
                )
            ->string($hateoas->serialize($resource, 'json'))
                ->isEqualTo(<<<JSON
{"page":2,"limit":10,"_links":{"self":{"href":"\/users?page=2"},"next":{"href":"\/users?page=3"}},"_embedded":{"user":["Adrien","William"],"test":"test"}}
JSON
                )
        ;
    }
}

<?php

namespace tests\Hateoas\Representation;

use Hateoas\Model\Embed;
use Hateoas\Model\Link;
use Hateoas\Representation\Resource as TestedResource;
use Hateoas\HateoasBuilder;
use tests\fixtures\Computer;use tests\fixtures\Smartphone;
use tests\TestCase;

class Resource extends TestCase
{
    public function test()
    {
        $resource = new TestedResource(array(
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
            new Embed('tech', array(
                new Computer('Mac'),
                new Smartphone('iPhone'),
            )),
            new Embed('test', 'test'),
        ), 'users');

        $hateoas = HateoasBuilder::buildHateoas();
        $halHateoas = HateoasBuilder::create()
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
  <entry rel="tech">
    <computer>
      <name><![CDATA[Mac]]></name>
    </computer>
    <smartphone>
      <name><![CDATA[iPhone]]></name>
    </smartphone>
  </entry>
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
  <resource rel="tech">
    <name><![CDATA[Mac]]></name>
  </resource>
  <resource rel="tech">
    <name><![CDATA[iPhone]]></name>
  </resource>
  <resource rel="test"><![CDATA[test]]></resource>
</resource>

XML
            )
            ->string($hateoas->serialize($resource, 'json'))
            ->isEqualTo(<<<JSON
{"page":2,"limit":10,"_links":{"self":{"href":"\/users?page=2"},"next":{"href":"\/users?page=3"}},"_embedded":{"user":["Adrien","William"],"tech":[{"name":"Mac"},{"name":"iPhone"}],"test":"test"}}
JSON
            )
        ;
    }
}

<?php

namespace Hateoas\Tests\Functional;

use Hateoas\Collection;
use Hateoas\Hateoas;
use Hateoas\Link;
use Hateoas\Resource;
use Hateoas\Tests\Fixtures\DataClass1;
use Hateoas\Tests\TestCase;

class SerializerTest extends TestCase
{
    public function testSerializeResourceInXml()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            new DataClass1('foo'),
            array(new Link('/foo', Link::REL_SELF))
        );

        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<data_class>
  <link href="/foo" rel="self"/>
  <content><![CDATA[foo]]></content>
</data_class>
XML
        , trim($serializer->serialize($res, 'xml')));
    }

    public function testSerializeArrayResourceInXml()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            array('foo'),
            array(new Link('/foo', Link::REL_SELF))
        );

        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <link href="/foo" rel="self"/>
  <entry><![CDATA[foo]]></entry>
</result>
XML
        , trim($serializer->serialize($res, 'xml')));
    }

    public function testSerializeCollectionInXml()
    {
        $serializer = Hateoas::getSerializer();
        $col        = new Collection(
            array(new Resource(
                new DataClass1('foo'),
                array(new Link('/foo', Link::REL_SELF))
            )),
            array(new Link('/foobar', Link::REL_SELF)),
            1
        );

        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<resources total="1">
  <link href="/foobar" rel="self"/>
  <data_class>
    <link href="/foo" rel="self"/>
    <content><![CDATA[foo]]></content>
  </data_class>
</resources>
XML
        , trim($serializer->serialize($col, 'xml')));
    }

    public function testSerializeResourceInJson()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            new DataClass1('foo'),
            array(new Link('/foo', Link::REL_SELF))
        );

        $this->assertEquals(
            '{"content":"foo","_links":{"self":{"href":"\/foo"}}}',
            $serializer->serialize($res, 'json')
        );
    }

    public function testSerializeCollectionInJson()
    {
        $serializer = Hateoas::getSerializer();
        $col        = new Collection(
            array(new Resource(
                new DataClass1('foo'),
                array(new Link('/foo', Link::REL_SELF))
            )),
            array(new Link('/foobar', Link::REL_SELF)),
            1
        );

        $this->assertEquals(
            '{"total":1,"_links":{"self":{"href":"\/foobar"}},"resources":[{"content":"foo","_links":{"self":{"href":"\/foo"}}}]}',
            $serializer->serialize($col, 'json')
        );
    }

    public function testSerializeCollectionWithTypesInJson()
    {
        $serializer = Hateoas::getSerializer();
        $col        = new Collection(
            array(new Resource(
                new DataClass1('foo'),
                array(new Link('/foo', Link::REL_SELF, 'application/vnd.hateoas.data_class'))
            )),
            array(new Link('/foobar', Link::REL_SELF, 'application/vnd.hateoas.data_class')),
            1
        );

        $this->assertEquals(
            '{"total":1,"_links":{"self":{"href":"\/foobar","type":"application\/vnd.hateoas.data_class"}},"resources":[{"content":"foo","_links":{"self":{"href":"\/foo","type":"application\/vnd.hateoas.data_class"}}}]}',
            $serializer->serialize($col, 'json')
        );
    }
}

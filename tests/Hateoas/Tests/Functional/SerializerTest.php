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
<collection total="1">
  <link href="/foobar" rel="self"/>
  <data_class>
    <link href="/foo" rel="self"/>
    <content><![CDATA[foo]]></content>
  </data_class>
</collection>
XML
        , trim($serializer->serialize($col, 'xml')));
    }
}

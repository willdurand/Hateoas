<?php

namespace Hateoas\Tests\Functional;

use Hateoas\Collection;
use Hateoas\Hateoas;
use Hateoas\Link;
use Hateoas\Resource;
use Hateoas\Tests\Fixtures\DataClass1;
use Hateoas\Tests\Fixtures\DummyClass;
use Hateoas\Tests\Fixtures\FormClass;
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

    public function testSerializeResourceWithOneEmbedInXml()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            new DataClass1('foo'),
            array(new Link('/foo', Link::REL_SELF)),
            array(),
            array(
                'dummy-class' => new Resource(
                    new DummyClass(),
                    array(new Link('/dummy', Link::REL_SELF))
                ),
            )
        );

        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<data_class>
  <link href="/foo" rel="self"/>
  <dummy-class>
    <link href="/dummy" rel="self"/>
  </dummy-class>
  <content><![CDATA[foo]]></content>
</data_class>
XML
        , trim($serializer->serialize($res, 'xml')));
    }

    public function testSerializeResourceWithTwoEmbedInXml()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            new DataClass1('foo'),
            array(new Link('/foo', Link::REL_SELF)),
            array(),
            array(
                'data-class1' => new Resource(
                    new DataClass1('data1'),
                    array(new Link('/data1', Link::REL_SELF))
                ),
                'dummy-class2' => new Resource(
                    new DummyClass(),
                    array(new Link('/dummy2', Link::REL_SELF))
                ),
            )
        );

        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<data_class>
  <link href="/foo" rel="self"/>
  <data-class1>
    <link href="/data1" rel="self"/>
    <content><![CDATA[data1]]></content>
  </data-class1>
  <dummy-class2>
    <link href="/dummy2" rel="self"/>
  </dummy-class2>
  <content><![CDATA[foo]]></content>
</data_class>
XML
        , trim($serializer->serialize($res, 'xml')));
    }


    public function testSerializeResourceWithOneEmbedCollectionInXml()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            new DataClass1('foo'),
            array(new Link('/foo', Link::REL_SELF)),
            array(),
            array(
                'dummy-classes' => array(
                    new Resource(
                        new DataClass1('data1'),
                        array(new Link('/data1', Link::REL_SELF))
                    ),
                    new Resource(
                        new DataClass1('data2'),
                        array(new Link('/data2', Link::REL_SELF))
                    ),
                ),
            )
        );

        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<data_class>
  <link href="/foo" rel="self"/>
  <dummy-classes>
    <data_class>
      <link href="/data1" rel="self"/>
      <content><![CDATA[data1]]></content>
    </data_class>
    <data_class>
      <link href="/data2" rel="self"/>
      <content><![CDATA[data2]]></content>
    </data_class>
  </dummy-classes>
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

    public function testSerializeResourceWithMultipleRelInXml()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            new DataClass1('foo'),
            array(
                new Link('/foo/12', 'foo', 'application/vnd.hateoas.foo'),
                new Link('/foo/34', 'foo', 'application/vnd.hateoas.foo'),
                new Link('/foo/56', 'foo', 'application/vnd.hateoas.foo')
            )
        );

        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<data_class>
  <link href="/foo/12" rel="foo" type="application/vnd.hateoas.foo"/>
  <link href="/foo/34" rel="foo" type="application/vnd.hateoas.foo"/>
  <link href="/foo/56" rel="foo" type="application/vnd.hateoas.foo"/>
  <content><![CDATA[foo]]></content>
</data_class>
XML
        , trim($serializer->serialize($res, 'xml')));
    }

    public function testSerializeResourceWithForm()
    {
        $serializer = Hateoas::getSerializer();
        $res = new Resource(new DataClass1('foo'));
        $res->setForms(array('form-new' => new FormClass()));

        $this->assertSame(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<data_class>
  <form-new>
    <textarea><![CDATA[form_textarea]]></textarea>
  </form-new>
  <content><![CDATA[foo]]></content>
</data_class>
XML
        , trim($serializer->serialize($res, 'xml')));
    }

    public function testSerializeCollectionInXml()
    {
        $serializer = Hateoas::getSerializer();
        $col        = new Collection(
            null,
            array(
                new Resource(
                    new DataClass1('foo'),
                    array(new Link('/foo', Link::REL_SELF))
                ),
                new Resource(
                    new DataClass1('bar'),
                    array(new Link('/bar', Link::REL_SELF))
                ),
            ),
            array(
                new Link('/foobar', Link::REL_SELF)
            ),
            2
        );

        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<resources total="2">
  <link href="/foobar" rel="self"/>
  <data_class>
    <link href="/foo" rel="self"/>
    <content><![CDATA[foo]]></content>
  </data_class>
  <data_class>
    <link href="/bar" rel="self"/>
    <content><![CDATA[bar]]></content>
  </data_class>
</resources>
XML
        , trim($serializer->serialize($col, 'xml')));
    }

    public function testSerializeCollectionWithOneElementInXml()
    {
        $serializer = Hateoas::getSerializer();
        $col        = new Collection(
            null,
            array(
                new Resource(
                    new DataClass1('foo'),
                    array(new Link('/foo', Link::REL_SELF))
                )
            ),
            array(
                new Link('/foobar', Link::REL_SELF)
            ),
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

    public function testSerializeCollectionWithRootNameInXml()
    {
        $serializer = Hateoas::getSerializer();
        $col        = new Collection(
            'data_classes',
            array(
                new Resource(
                    new DataClass1('foo'),
                    array(new Link('/foo', Link::REL_SELF))
                )
            ),
            array(
                new Link('/foobar', Link::REL_SELF)
            ),
            1
        );

        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<data_classes total="1">
  <link href="/foobar" rel="self"/>
  <data_class>
    <link href="/foo" rel="self"/>
    <content><![CDATA[foo]]></content>
  </data_class>
</data_classes>
XML
        , trim($serializer->serialize($col, 'xml')));
    }

    public function testSerializeCollectionWithForm()
    {
        $serializer = Hateoas::getSerializer();
        $collection = new Collection('data_classes', array(new Resource(new DataClass1('foo'))));
        $collection->setForms(array('form-new' => new FormClass()));

        $this->assertSame(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<data_classes>
  <data_class>
    <content><![CDATA[foo]]></content>
  </data_class>
  <form-new>
    <textarea><![CDATA[form_textarea]]></textarea>
  </form-new>
</data_classes>
XML
        , trim($serializer->serialize($collection, 'xml')));
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

    public function testSerializeResourceWithTypeInJson()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            new DataClass1('foo'),
            array(new Link('/foo', Link::REL_SELF, 'application/vnd.hateoas.data_class'))
        );

        $this->assertEquals(
            '{"content":"foo","_links":{"self":{"href":"\/foo","type":"application\/vnd.hateoas.data_class"}}}',
            $serializer->serialize($res, 'json')
        );
    }

    public function testSerializeResourceWithMultipleRelInJson()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            new DataClass1('foo'),
            array(
                new Link('/foo/12', 'foo', 'application/vnd.hateoas.foo'),
                new Link('/foo/34', 'foo', 'application/vnd.hateoas.foo'),
                new Link('/foo/56', 'foo', 'application/vnd.hateoas.foo')
            )
        );

        $this->assertEquals(
            '{"content":"foo","_links":{"foo":[{"href":"\/foo\/12","type":"application\/vnd.hateoas.foo"},{"href":"\/foo\/34","type":"application\/vnd.hateoas.foo"},{"href":"\/foo\/56","type":"application\/vnd.hateoas.foo"}]}}',
            $serializer->serialize($res, 'json')
        );
    }

    public function testSerializeCollectionWithOneElementInJson()
    {
        $serializer = Hateoas::getSerializer();
        $col        = new Collection(
            null,
            array(
                new Resource(
                    new DataClass1('foo'),
                    array(new Link('/foo', Link::REL_SELF))
                ),
            ),
            array(new Link('/foobar', Link::REL_SELF)),
            1
        );

        $this->assertEquals(
            '{"total":1,"_links":{"self":{"href":"\/foobar"}},"resources":[{"content":"foo","_links":{"self":{"href":"\/foo"}}}]}',
            $serializer->serialize($col, 'json')
        );
    }

    public function testSerializeCollectionInJson()
    {
        $serializer = Hateoas::getSerializer();
        $col        = new Collection(
            null,
            array(
                new Resource(
                    new DataClass1('foo'),
                    array(new Link('/foo', Link::REL_SELF))
                ),
                new Resource(
                    new DataClass1('bar'),
                    array(new Link('/bar', Link::REL_SELF))
                ),
            ),
            array(new Link('/foobar', Link::REL_SELF)),
            2
        );

        $this->assertEquals(
            '{"total":2,"_links":{"self":{"href":"\/foobar"}},"resources":[{"content":"foo","_links":{"self":{"href":"\/foo"}}},{"content":"bar","_links":{"self":{"href":"\/bar"}}}]}',
            $serializer->serialize($col, 'json')
        );
    }

    public function testSerializeCollectionWithRootNameInJson()
    {
        $serializer = Hateoas::getSerializer();
        $col        = new Collection(
            'data_classes',
            array(
                new Resource(
                    new DataClass1('foo'),
                    array(new Link('/foo', Link::REL_SELF))
                ),
                new Resource(
                    new DataClass1('bar'),
                    array(new Link('/bar', Link::REL_SELF))
                ),
            ),
            array(new Link('/foobar', Link::REL_SELF)),
            2
        );

        $this->assertEquals(
            '{"total":2,"_links":{"self":{"href":"\/foobar"}},"data_classes":[{"content":"foo","_links":{"self":{"href":"\/foo"}}},{"content":"bar","_links":{"self":{"href":"\/bar"}}}]}',
            $serializer->serialize($col, 'json')
        );
    }

    public function testSerializeCollectionWithTypesInJson()
    {
        $serializer = Hateoas::getSerializer();
        $col        = new Collection(
            null,
            array(
                new Resource(
                    new DataClass1('foo'),
                    array(new Link('/foo', Link::REL_SELF, 'application/vnd.hateoas.data_class'))
                )
            ),
            array(
                new Link('/foobar', Link::REL_SELF, 'application/vnd.hateoas.data_class')
            ),
            10,
            1,
            2
        );

        $this->assertEquals(
            '{"total":10,"page":1,"limit":2,"_links":{"self":{"href":"\/foobar","type":"application\/vnd.hateoas.data_class"}},"resources":[{"content":"foo","_links":{"self":{"href":"\/foo","type":"application\/vnd.hateoas.data_class"}}}]}',
            $serializer->serialize($col, 'json')
        );
    }

    public function testSerializeResourceWithOneEmbedInJson()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            new DataClass1('foo'),
            array(new Link('/foo', Link::REL_SELF)),
            array(),
            array(
                'dummy-class' => new Resource(
                    new DummyClass(),
                    array(new Link('/dummy', Link::REL_SELF))
                ),
            )
        );

        $this->assertEquals('{"content":"foo","_links":{"self":{"href":"\/foo"}},"_embeds":{"dummy-class":{"_links":{"self":{"href":"\/dummy"}}}}}'
            , $serializer->serialize($res, 'json'));
    }

    public function testSerializeResourceWithTwoEmbedInJson()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            new DataClass1('foo'),
            array(new Link('/foo', Link::REL_SELF)),
            array(),
            array(
                'data-class1' => new Resource(
                    new DataClass1('data1'),
                    array(new Link('/data1', Link::REL_SELF))
                ),
                'dummy-class2' => new Resource(
                    new DummyClass(),
                    array(new Link('/dummy2', Link::REL_SELF))
                ),
            )
        );

        $this->assertEquals('{"content":"foo","_links":{"self":{"href":"\/foo"}},"_embeds":{"data-class1":{"content":"data1","_links":{"self":{"href":"\/data1"}}},"dummy-class2":{"_links":{"self":{"href":"\/dummy2"}}}}}'
            , trim($serializer->serialize($res, 'json')));
    }

    public function testSerializeResourceWithOneEmbedCollectionInJson()
    {
        $serializer = Hateoas::getSerializer();
        $res        = new Resource(
            new DataClass1('foo'),
            array(new Link('/foo', Link::REL_SELF)),
            array(),
            array(
                'dummy-classes' => array(
                    new Resource(
                        new DataClass1('data1'),
                        array(new Link('/data1', Link::REL_SELF))
                    ),
                    new Resource(
                        new DataClass1('data2'),
                        array(new Link('/data2', Link::REL_SELF))
                    ),
                ),
            )
        );

        $this->assertEquals('{"content":"foo","_links":{"self":{"href":"\/foo"}},"_embeds":{"dummy-classes":[{"content":"data1","_links":{"self":{"href":"\/data1"}}},{"content":"data2","_links":{"self":{"href":"\/data2"}}}]}}'
            , trim($serializer->serialize($res, 'json')));
    }
}

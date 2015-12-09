<?php

namespace Hateoas\Tests\Representation;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;

class CollectionRepresentationTest extends RepresentationTestCase
{
    /**
     * @dataProvider getTestSerializeData
     */
    public function testSerialize($resources)
    {
        $collection = new CollectionRepresentation(
            $resources,
            'authors'
        );
        $collection->setXmlElementName('users');

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );
        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
</collection>

XML
            ,
            $this->halHateoas->serialize($collection, 'xml')
        );

        $this->assertSame(
            <<<JSON
{
    "_embedded": {
        "authors": [
            "Adrien",
            "William"
        ]
    }
}
JSON
            ,
            $this->json($this->halHateoas->serialize($collection, 'json'))
        );
    }

    public function getTestSerializeData()
    {
        return array(
            array(
                array(
                    'Adrien',
                    'William',
                )
            ),
            array(
                new \ArrayIterator(array(
                    'Adrien',
                    'William',
                ))
            ),
        );
    }

    public function testEmbeddedRelationIsMergedWithCustomRelations()
    {
        $collection = new CollectionRepresentation(
            array(
                'Adrien',
                'William',
            ),
            'authors',
            null,
            null,
            null,
            array(
                new Relation(
                    'custom',
                    new Route('/custom')
                ),
            )
        );
        $collection->setXmlElementName('users');

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <link rel="custom" href="/custom"/>
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );
        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <link rel="custom" href="/custom"/>
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
</collection>

XML
            ,
            $this->halHateoas->serialize($collection, 'xml')
        );

        $this->assertSame(
            <<<JSON
{
    "_links": {
        "custom": {
            "href": "\/custom"
        }
    },
    "_embedded": {
        "authors": [
            "Adrien",
            "William"
        ]
    }
}
JSON
            ,
            $this->json($this->halHateoas->serialize($collection, 'json'))
        );
    }
}

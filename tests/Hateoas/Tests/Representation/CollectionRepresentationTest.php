<?php

namespace Hateoas\Tests\Representation;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;

class CollectionRepresentationTest extends RepresentationTestCase
{
    public function testSerialize()
    {
        $collection = new CollectionRepresentation(
            array(
                'Adrien',
                'William',
            ),
            'authors'
        );
        $collection->setXmlElementName('users');

        $this
            ->string($this->hateoas->serialize($collection, 'xml'))
            ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
</collection>

XML
            )
            ->string($this->halHateoas->serialize($collection, 'xml'))
            ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
</collection>

XML
            );

        $this
            ->json($this->halHateoas->serialize($collection, 'json'))
            ->isEqualTo(<<<JSON
{
    "_embedded": {
        "authors": [
            "Adrien",
            "William"
        ]
    }
}
JSON
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

        $this
            ->string($this->hateoas->serialize($collection, 'xml'))
            ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <link rel="custom" href="/custom"/>
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
</collection>

XML
            )
            ->string($this->halHateoas->serialize($collection, 'xml'))
            ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <link rel="custom" href="/custom"/>
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
</collection>

XML
            );

        $this
            ->json($this->halHateoas->serialize($collection, 'json'))
            ->isEqualTo(<<<JSON
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
            );
    }
}

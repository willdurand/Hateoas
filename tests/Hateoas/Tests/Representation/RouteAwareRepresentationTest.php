<?php

namespace Hateoas\Tests\Representation;

use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\RouteAwareRepresentation;

class RouteAwareRepresentationTest extends RepresentationTestCase
{
    public function testSerialize()
    {
        $collection = new RouteAwareRepresentation(
            new CollectionRepresentation(
                array(
                    'Adrien',
                    'William',
                ),
                'authors',
                'users'
            ),
            '/authors',
            array(
                'query' => 'willdurand/Hateoas',
            )
        );

        $this
            ->string($this->hateoas->serialize($collection, 'xml'))
            ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
  <link rel="self" href="/authors?query=willdurand%2FHateoas"/>
</result>

XML
            )
            ->string($this->halHateoas->serialize($collection, 'xml'))
            ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result href="/authors?query=willdurand%2FHateoas">
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
</result>

XML
            );

        $this
            ->json($this->halHateoas->serialize($collection, 'json'))
            ->isEqualTo(<<<JSON
{
    "_links": {
        "self": {
            "href": "\/authors?query=willdurand%2FHateoas"
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

    public function testGenerateAbsoluteURIs()
    {
        $collection = new RouteAwareRepresentation(
            new CollectionRepresentation(
                array(
                    'Adrien',
                    'William',
                ),
                'authors',
                'users'
            ),
            '/authors',
            array(
                'query' => 'willdurand/Hateoas',
            ),
            true // absolute
        );

        $this
            ->string($this->hateoas->serialize($collection, 'xml'))
            ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
  <link rel="self" href="http://example.com/authors?query=willdurand%2FHateoas"/>
</result>

XML
            )
            ->string($this->halHateoas->serialize($collection, 'xml'))
            ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result href="http://example.com/authors?query=willdurand%2FHateoas">
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
</result>

XML
            );

        $this
            ->json($this->halHateoas->serialize($collection, 'json'))
            ->isEqualTo(<<<JSON
{
    "_links": {
        "self": {
            "href": "http:\/\/example.com\/authors?query=willdurand%2FHateoas"
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

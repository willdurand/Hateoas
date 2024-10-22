<?php

declare(strict_types=1);

namespace Hateoas\Tests\Representation;

use Doctrine\Common\Annotations\AnnotationReader;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Tests\Fixtures\Attribute;
use Hateoas\Tests\Fixtures\UsersRepresentation;

class PaginatedRepresentationTest extends RepresentationTestCase
{
    public function testSerialize()
    {
        $collection = new PaginatedRepresentation(
            new CollectionRepresentation(
                [
                    'Adrien',
                    'William',
                ]
            ),
            '/authors',
            ['query' => 'willdurand/Hateoas'],
            3,
            20,
            17,
            null,
            null,
            false,
            100
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="3" limit="20" pages="17" total="100">
  <link rel="self" href="/authors?query=willdurand%2FHateoas&amp;page=3&amp;limit=20"/>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;page=1&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;page=17&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;page=4&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;page=2&amp;limit=20"/>
  <entry rel="items">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </entry>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );
        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="3" limit="20" pages="17" total="100" href="/authors?query=willdurand%2FHateoas&amp;page=3&amp;limit=20">
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;page=1&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;page=17&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;page=4&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;page=2&amp;limit=20"/>
  <resource rel="items"><![CDATA[Adrien]]></resource>
  <resource rel="items"><![CDATA[William]]></resource>
</collection>

XML
            ,
            $this->halHateoas->serialize($collection, 'xml')
        );

        if (class_exists(AnnotationReader::class)) {
            $usersRepresentation = new UsersRepresentation($collection);
        } else {
            $usersRepresentation = new Attribute\UsersRepresentation($collection);
        }

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<users page="3" limit="20" pages="17" total="100">
  <link rel="self" href="/authors?query=willdurand%2FHateoas&amp;page=3&amp;limit=20"/>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;page=1&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;page=17&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;page=4&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;page=2&amp;limit=20"/>
  <entry rel="items">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </entry>
</users>

XML
            ,
            $this->hateoas->serialize($usersRepresentation, 'xml')
        );
        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<users page="3" limit="20" pages="17" total="100" href="/authors?query=willdurand%2FHateoas&amp;page=3&amp;limit=20">
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;page=1&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;page=17&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;page=4&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;page=2&amp;limit=20"/>
  <resource rel="items"><![CDATA[Adrien]]></resource>
  <resource rel="items"><![CDATA[William]]></resource>
</users>

XML
            ,
            $this->halHateoas->serialize($usersRepresentation, 'xml')
        );
        $this->assertSame(
            '{'
                . '"page":3,'
                . '"limit":20,'
                . '"pages":17,'
                . '"total":100,'
                . '"_links":{'
                    . '"self":{'
                        . '"href":"\/authors?query=willdurand%2FHateoas&page=3&limit=20"'
                    . '},'
                    . '"first":{'
                        . '"href":"\/authors?query=willdurand%2FHateoas&page=1&limit=20"'
                    . '},'
                    . '"last":{'
                        . '"href":"\/authors?query=willdurand%2FHateoas&page=17&limit=20"'
                    . '},'
                    . '"next":{'
                        . '"href":"\/authors?query=willdurand%2FHateoas&page=4&limit=20"'
                    . '},'
                    . '"previous":{'
                        . '"href":"\/authors?query=willdurand%2FHateoas&page=2&limit=20"'
                    . '}'
                . '},'
                . '"_embedded":{'
                    . '"items":['
                        . '"Adrien",'
                        . '"William"'
                    . ']'
                . '}'
            . '}',
            $this->halHateoas->serialize($collection, 'json')
        );
    }

    public function testGenerateAbsoluteURIs()
    {
        $collection = new PaginatedRepresentation(
            new CollectionRepresentation(
                [
                    'Adrien',
                    'William',
                ]
            ),
            '/authors',
            ['query' => 'willdurand/Hateoas'],
            3,
            20,
            17,
            null,
            null,
            true // force absolute URIs
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="3" limit="20" pages="17">
  <link rel="self" href="http://example.com/authors?query=willdurand%2FHateoas&amp;page=3&amp;limit=20"/>
  <link rel="first" href="http://example.com/authors?query=willdurand%2FHateoas&amp;page=1&amp;limit=20"/>
  <link rel="last" href="http://example.com/authors?query=willdurand%2FHateoas&amp;page=17&amp;limit=20"/>
  <link rel="next" href="http://example.com/authors?query=willdurand%2FHateoas&amp;page=4&amp;limit=20"/>
  <link rel="previous" href="http://example.com/authors?query=willdurand%2FHateoas&amp;page=2&amp;limit=20"/>
  <entry rel="items">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </entry>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );
        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="3" limit="20" pages="17" href="http://example.com/authors?query=willdurand%2FHateoas&amp;page=3&amp;limit=20">
  <link rel="first" href="http://example.com/authors?query=willdurand%2FHateoas&amp;page=1&amp;limit=20"/>
  <link rel="last" href="http://example.com/authors?query=willdurand%2FHateoas&amp;page=17&amp;limit=20"/>
  <link rel="next" href="http://example.com/authors?query=willdurand%2FHateoas&amp;page=4&amp;limit=20"/>
  <link rel="previous" href="http://example.com/authors?query=willdurand%2FHateoas&amp;page=2&amp;limit=20"/>
  <resource rel="items"><![CDATA[Adrien]]></resource>
  <resource rel="items"><![CDATA[William]]></resource>
</collection>

XML
            ,
            $this->halHateoas->serialize($collection, 'xml')
        );
        $this->assertSame(
            '{'
                . '"page":3,'
                . '"limit":20,'
                . '"pages":17,'
                . '"_links":{'
                    . '"self":{'
                        . '"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&page=3&limit=20"'
                    . '},'
                    . '"first":{'
                        . '"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&page=1&limit=20"'
                    . '},'
                    . '"last":{'
                        . '"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&page=17&limit=20"'
                    . '},'
                    . '"next":{'
                        . '"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&page=4&limit=20"'
                    . '},'
                    . '"previous":{'
                        . '"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&page=2&limit=20"'
                    . '}'
                . '},'
                . '"_embedded":{'
                    . '"items":['
                        . '"Adrien",'
                        . '"William"'
                    . ']'
                . '}'
            . '}',
            $this->halHateoas->serialize($collection, 'json')
        );
    }
}

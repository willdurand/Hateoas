<?php

declare(strict_types=1);

namespace Hateoas\Tests\Representation;

use Doctrine\Common\Annotations\AnnotationReader;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\OffsetRepresentation;
use Hateoas\Tests\Fixtures\Attribute;
use Hateoas\Tests\Fixtures\UsersRepresentation;

class OffsetRepresentationTest extends RepresentationTestCase
{
    public function testSerialize()
    {
        $collection = new OffsetRepresentation(
            new CollectionRepresentation(
                [
                    'Adrien',
                    'William',
                ]
            ),
            '/authors',
            ['query' => 'willdurand/Hateoas'],
            44,
            20,
            95,
            null,
            null,
            false
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection offset="44" limit="20" total="95">
  <link rel="self" href="/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20"/>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
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
<collection offset="44" limit="20" total="95" href="/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20">
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
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
<users offset="44" limit="20" total="95">
  <link rel="self" href="/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20"/>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
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
<users offset="44" limit="20" total="95" href="/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20">
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
  <resource rel="items"><![CDATA[Adrien]]></resource>
  <resource rel="items"><![CDATA[William]]></resource>
</users>

XML
            ,
            $this->halHateoas->serialize($usersRepresentation, 'xml')
        );

        $this->assertSame(
            '{'
                . '"offset":44,'
                . '"limit":20,'
                . '"total":95,'
                . '"_links":{'
                    . '"self":{'
                        . '"href":"\/authors?query=willdurand%2FHateoas&offset=44&limit=20"'
                    . '},'
                    . '"first":{'
                        . '"href":"\/authors?query=willdurand%2FHateoas&limit=20"'
                    . '},'
                    . '"last":{'
                        . '"href":"\/authors?query=willdurand%2FHateoas&offset=80&limit=20"'
                    . '},'
                    . '"next":{'
                        . '"href":"\/authors?query=willdurand%2FHateoas&offset=64&limit=20"'
                    . '},'
                    . '"previous":{'
                        . '"href":"\/authors?query=willdurand%2FHateoas&offset=24&limit=20"'
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
        $collection = new OffsetRepresentation(
            new CollectionRepresentation(
                [
                    'Adrien',
                    'William',
                ]
            ),
            '/authors',
            ['query' => 'willdurand/Hateoas'],
            44,
            20,
            95,
            null,
            null,
            true // force absolute URIs
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection offset="44" limit="20" total="95">
  <link rel="self" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20"/>
  <link rel="first" href="http://example.com/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
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
<collection offset="44" limit="20" total="95" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20">
  <link rel="first" href="http://example.com/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
  <resource rel="items"><![CDATA[Adrien]]></resource>
  <resource rel="items"><![CDATA[William]]></resource>
</collection>

XML
            ,
            $this->halHateoas->serialize($collection, 'xml')
        );
        $this->assertSame(
            '{'
                . '"offset":44,'
                . '"limit":20,'
                . '"total":95,'
                . '"_links":{'
                    . '"self":{'
                        . '"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&offset=44&limit=20"'
                    . '},'
                    . '"first":{'
                        . '"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&limit=20"'
                    . '},'
                    . '"last":{'
                        . '"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&offset=80&limit=20"'
                    . '},'
                    . '"next":{'
                        . '"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&offset=64&limit=20"'
                    . '},'
                    . '"previous":{'
                        . '"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&offset=24&limit=20"'
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

    public function testExclusion()
    {
        $inline = new CollectionRepresentation(
            [
                'Adrien',
                'William',
            ],
            'authors',
            'users'
        );

        /*
         * no last entry since `total` is missing
         * no previous only when offset is 0/null
         */
        $collection = new OffsetRepresentation(
            $inline,
            '/authors',
            ['query' => 'willdurand/Hateoas'],
            null,
            20
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection limit="20">
  <link rel="self" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;offset=20&amp;limit=20"/>
  <entry rel="items">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </entry>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );

        /*
         * no next since on last block
         */
        $collection = new OffsetRepresentation(
            $inline,
            '/authors',
            ['query' => 'willdurand/Hateoas'],
            80,
            20,
            100
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection offset="80" limit="20" total="100">
  <link rel="self" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;offset=60&amp;limit=20"/>
  <entry rel="items">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </entry>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );
    }
}

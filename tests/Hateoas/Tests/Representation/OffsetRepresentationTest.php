<?php

namespace Hateoas\Tests\Representation;

use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\OffsetRepresentation;
use Hateoas\Tests\Fixtures\UsersRepresentation;

class OffsetRepresentationTest extends RepresentationTestCase
{
    public function testSerialize()
    {
        $collection = new OffsetRepresentation(
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
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
  <link rel="self" href="/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20"/>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection offset="44" limit="20" total="95" href="/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20">
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
</collection>

XML
            ,
            $this->halHateoas->serialize($collection, 'xml')
        );
        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<users offset="44" limit="20" total="95">
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
  <link rel="self" href="/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20"/>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
</users>

XML
            ,
            $this->hateoas->serialize(new UsersRepresentation($collection), 'xml')
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<users offset="44" limit="20" total="95" href="/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20">
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
</users>

XML
            ,
            $this->halHateoas->serialize(new UsersRepresentation($collection), 'xml')
        );

        $this->assertSame(
            '{'
                .'"offset":44,'
                .'"limit":20,'
                .'"total":95,'
                .'"_links":{'
                    .'"self":{'
                        .'"href":"\/authors?query=willdurand%2FHateoas&offset=44&limit=20"'
                    .'},'
                    .'"first":{'
                        .'"href":"\/authors?query=willdurand%2FHateoas&limit=20"'
                    .'},'
                    .'"last":{'
                        .'"href":"\/authors?query=willdurand%2FHateoas&offset=80&limit=20"'
                    .'},'
                    .'"next":{'
                        .'"href":"\/authors?query=willdurand%2FHateoas&offset=64&limit=20"'
                    .'},'
                    .'"previous":{'
                        .'"href":"\/authors?query=willdurand%2FHateoas&offset=24&limit=20"'
                    .'}'
                .'},'
                .'"_embedded":{'
                    .'"authors":['
                        .'"Adrien",'
                        .'"William"'
                    .']'
                .'}'
            .'}',
            $this->halHateoas->serialize($collection, 'json')
        );
    }

    public function testGenerateAbsoluteURIs()
    {
        $collection = new OffsetRepresentation(
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
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
  <link rel="self" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20"/>
  <link rel="first" href="http://example.com/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );
        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection offset="44" limit="20" total="95" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=44&amp;limit=20">
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
  <link rel="first" href="http://example.com/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="next" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=64&amp;limit=20"/>
  <link rel="previous" href="http://example.com/authors?query=willdurand%2FHateoas&amp;offset=24&amp;limit=20"/>
</collection>

XML
            ,
            $this->halHateoas->serialize($collection, 'xml')
        );
        $this->assertSame(
            '{'
                .'"offset":44,'
                .'"limit":20,'
                .'"total":95,'
                .'"_links":{'
                    .'"self":{'
                        .'"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&offset=44&limit=20"'
                    .'},'
                    .'"first":{'
                        .'"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&limit=20"'
                    .'},'
                    .'"last":{'
                        .'"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&offset=80&limit=20"'
                    .'},'
                    .'"next":{'
                        .'"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&offset=64&limit=20"'
                    .'},'
                    .'"previous":{'
                        .'"href":"http:\/\/example.com\/authors?query=willdurand%2FHateoas&offset=24&limit=20"'
                    .'}'
                .'},'
                .'"_embedded":{'
                    .'"authors":['
                        .'"Adrien",'
                        .'"William"'
                    .']'
                .'}'
            .'}',
            $this->halHateoas->serialize($collection, 'json')
        );
    }

    public function testExclusion()
    {
        $inline = new CollectionRepresentation(
            array(
                'Adrien',
                'William',
            ),
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
            array(
                'query' => 'willdurand/Hateoas',
            ),
            null,
            20
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection limit="20">
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
  <link rel="self" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;offset=20&amp;limit=20"/>
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
            array(
                'query' => 'willdurand/Hateoas',
            ),
            80,
            20,
            100
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection offset="80" limit="20" total="100">
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
  <link rel="self" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;offset=80&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;offset=60&amp;limit=20"/>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );
    }
}

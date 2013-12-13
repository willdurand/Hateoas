<?php

namespace Hateoas\Tests\Representation;

use Hateoas\Tests\TestCase;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use Hateoas\Representation\SimpleCollection;
use Hateoas\Representation\PaginatedCollection;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\HateoasBuilder;
use Hateoas\Serializer\XmlHalSerializer;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class PaginatedCollectionTest extends TestCase
{
    public function test()
    {
        $queryStringUrlGenerator = new CallableUrlGenerator(function ($route, array $parameters) {
            return $route . '?' . http_build_query($parameters);
        });
        $hateoas = HateoasBuilder::create()
            ->setUrlGenerator(null, $queryStringUrlGenerator)
            ->build()
        ;
        $halHateoas = HateoasBuilder::create()
            ->setUrlGenerator(null, $queryStringUrlGenerator)
            ->setXmlSerializer(new XmlHalSerializer())
            ->build()
        ;

        $collection = new PaginatedCollection(
            new SimpleCollection(
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
            3,
            20,
            17
        );

        $this
            ->string($hateoas->serialize($collection, 'xml'))
            ->isEqualTo(
                <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="3" limit="20" pages="17">
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
  <link rel="self" href="/authors?query=willdurand%2FHateoas&amp;page=3&amp;limit=20"/>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;page=1&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;page=17&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;page=4&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;page=2&amp;limit=20"/>
</collection>

XML
            )
            ->string($halHateoas->serialize($collection, 'xml'))
            ->isEqualTo(
                <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="3" limit="20" pages="17" href="/authors?query=willdurand%2FHateoas&amp;page=3&amp;limit=20">
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
  <link rel="first" href="/authors?query=willdurand%2FHateoas&amp;page=1&amp;limit=20"/>
  <link rel="last" href="/authors?query=willdurand%2FHateoas&amp;page=17&amp;limit=20"/>
  <link rel="next" href="/authors?query=willdurand%2FHateoas&amp;page=4&amp;limit=20"/>
  <link rel="previous" href="/authors?query=willdurand%2FHateoas&amp;page=2&amp;limit=20"/>
</collection>

XML
            )
            ->string($halHateoas->serialize($collection, 'json'))
            ->isEqualTo(
                '{'
                    .'"page":3,'
                    .'"limit":20,'
                    .'"pages":17,'
                    .'"_links":{'
                        .'"self":{'
                            .'"href":"\/authors?query=willdurand%2FHateoas&page=3&limit=20"'
                        .'},'
                        .'"first":{'
                            .'"href":"\/authors?query=willdurand%2FHateoas&page=1&limit=20"'
                        .'},'
                        .'"last":{'
                            .'"href":"\/authors?query=willdurand%2FHateoas&page=17&limit=20"'
                        .'},'
                        .'"next":{'
                            .'"href":"\/authors?query=willdurand%2FHateoas&page=4&limit=20"'
                        .'},'
                        .'"previous":{'
                            .'"href":"\/authors?query=willdurand%2FHateoas&page=2&limit=20"'
                        .'}'
                    .'},'
                    .'"_embedded":{'
                        .'"authors":['
                            .'"Adrien",'
                            .'"William"'
                        .']'
                    .'}'
                .'}'
            )
        ;
    }

    public function testWithPagerfanta()
    {
        $hateoas = HateoasBuilder::create()
            ->setUrlGenerator(null, new CallableUrlGenerator(function ($route, array $parameters) {
                return $route . '?' . http_build_query($parameters);
            }))
            ->build();

        $factory    = new PagerfantaFactory();
        $pagerfanta = new Pagerfanta(new ArrayAdapter(array(
            'bim',
            'bam',
            'boom'
        )));

        $collection = $factory->create($pagerfanta, 'my_route');

        $this
            ->string($hateoas->serialize($collection, 'xml'))
            ->isEqualTo(
                <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="1" limit="10" pages="1">
  <entry><![CDATA[bim]]></entry>
  <entry><![CDATA[bam]]></entry>
  <entry><![CDATA[boom]]></entry>
  <link rel="self" href="my_route?page=1&amp;limit=10"/>
  <link rel="first" href="my_route?page=1&amp;limit=10"/>
  <link rel="last" href="my_route?page=1&amp;limit=10"/>
</collection>

XML
        );
    }
}

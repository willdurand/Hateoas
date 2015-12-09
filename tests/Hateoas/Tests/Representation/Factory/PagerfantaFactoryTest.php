<?php

namespace Hateoas\Tests\Representation\Factory;

use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Tests\Representation\RepresentationTestCase;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class PagerfantaFactoryTest extends RepresentationTestCase
{
    public function test()
    {
        $results = array(
            'Adrien',
            'William',
        );

        $pagerProphecy = $this->prophesize('Pagerfanta\Pagerfanta');
        $pagerProphecy->getCurrentPageResults()->willReturn($results);
        $pagerProphecy->getCurrentPage()->willReturn(2);
        $pagerProphecy->getMaxPerPage()->willReturn(20);
        $pagerProphecy->getNbPages()->willReturn(4);
        $pagerProphecy->getNbResults()->willReturn(100);

        $factory = new PagerfantaFactory('p', 'l');
        $representation1 = $factory->createRepresentation(
            $pagerProphecy->reveal(),
            new Route(
                'users',
                array(
                    'query' => 'hateoas',
                )
            )
        );
        $representation2 = $factory->createRepresentation(
            $pagerProphecy->reveal(),
            new Route(
                'users',
                array(
                    'query' => 'hateoas',
                )
            ),
            array()
        );

        $this->assertEquals(new CollectionRepresentation($results), $representation1->getInline());
        $this->assertSame([], $representation2->getInline());

        foreach (array($representation1, $representation2) as $representation) {
            $this->assertInstanceOf('Hateoas\Representation\PaginatedRepresentation', $representation);
            $this->assertSame(2, $representation->getPage());
            $this->assertSame(20, $representation->getLimit());
            $this->assertSame(4, $representation->getPages());
            $this->assertSame(100, $representation->getTotal());
            $this->assertSame(
                [
                    'query' => 'hateoas',
                    'p' => 2,
                    'l' => 20,
                ],
                $representation->getParameters()
            );
            $this->assertSame('p', $representation->getPageParameterName());
            $this->assertSame('l', $representation->getLimitParameterName());
        }
    }

    public function testSerialize()
    {
        $factory    = new PagerfantaFactory();
        $pagerfanta = new Pagerfanta(new ArrayAdapter(array(
            'bim',
            'bam',
            'boom'
        )));

        $collection = $factory->createRepresentation($pagerfanta, new Route('my_route'));

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="1" limit="10" pages="1" total="3">
  <entry rel="items">
    <entry><![CDATA[bim]]></entry>
    <entry><![CDATA[bam]]></entry>
    <entry><![CDATA[boom]]></entry>
  </entry>
  <link rel="self" href="my_route?page=1&amp;limit=10"/>
  <link rel="first" href="my_route?page=1&amp;limit=10"/>
  <link rel="last" href="my_route?page=1&amp;limit=10"/>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );
        $this->assertSame(
            <<<JSON
{
    "page": 1,
    "limit": 10,
    "pages": 1,
    "total": 3,
    "_links": {
        "self": {
            "href": "my_route?page=1&limit=10"
        },
        "first": {
            "href": "my_route?page=1&limit=10"
        },
        "last": {
            "href": "my_route?page=1&limit=10"
        }
    },
    "_embedded": {
        "items": [
            "bim",
            "bam",
            "boom"
        ]
    }
}
JSON
            ,
            $this->json($this->hateoas->serialize($collection, 'json'))
        );
    }

    public function testGenerateAbsoluteURIs()
    {
        $factory    = new PagerfantaFactory();
        $pagerfanta = new Pagerfanta(new ArrayAdapter(array(
            'bim',
            'bam',
            'boom'
        )));

        $collection = $factory->createRepresentation(
            $pagerfanta,
            new Route(
                '/my_route',
                array(),
                true
            )
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="1" limit="10" pages="1" total="3">
  <entry rel="items">
    <entry><![CDATA[bim]]></entry>
    <entry><![CDATA[bam]]></entry>
    <entry><![CDATA[boom]]></entry>
  </entry>
  <link rel="self" href="http://example.com/my_route?page=1&amp;limit=10"/>
  <link rel="first" href="http://example.com/my_route?page=1&amp;limit=10"/>
  <link rel="last" href="http://example.com/my_route?page=1&amp;limit=10"/>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );
    }
}

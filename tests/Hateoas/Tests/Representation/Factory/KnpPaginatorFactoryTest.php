<?php

namespace Hateoas\Tests\Representation\Factory;

use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\Factory\KnpPaginatorFactory;
use Hateoas\Tests\Representation\RepresentationTestCase;
use Knp\Component\Pager\Paginator;

class KnpPaginatorFactoryTest extends RepresentationTestCase {

    public function test()
    {

        $results = array(
            'Achille',
            'Skineur'
        );

        $pagerProhecy = $this->prophesize('Knp\Component\Pager\Pagination\SlidingPagination');
        $pagerProhecy->getItems()->willReturn($results);
        $pagerProhecy->getCurrentPageNumber()->willReturn(1);
        $pagerProhecy->getItemNumberPerPage()->willReturn(20);
        $pagerProhecy->getPaginationData()->willReturn(array('pageCount' => 3));
        $pagerProhecy->getTotalItemCount()->willReturn(50);

        $factory = new KnpPaginatorFactory('page', 'limit');

        $represention1 =  $factory->createRepresentation(
            $pagerProhecy->reveal(),
            new Route(
                'users',
                array(
                    'query' => 'hateoas'
                )
            ),
            null
        );
        $represention2 =  $factory->createRepresentation(
            $pagerProhecy->reveal(),
            new Route(
                'users',
                array(
                    'query' => 'hateoas'
                )
            ),
            null
        );
        $this->assertEquals(new CollectionRepresentation($results), $represention1->getInline());
        $this->assertEquals(new CollectionRepresentation($results), $represention2->getInline());

        foreach(array($represention1, $represention2) as $representation){
            $this->assertInstanceOf('Hateoas\Representation\PaginatedRepresentation', $representation);
            $this->assertSame(1, $representation->getPage());
            $this->assertSame(50, $representation->getTotal());
            $this->assertSame(20, $representation->getLimit());
            $this->assertSame(3, $representation->getPages());
            $this->assertSame('limit', $representation->getLimitParameterName());
            $this->assertSame('page', $representation->getPageParameterName());
            $this->assertSame(
                [
                    'query' => 'hateoas',
                    'page' => 1,
                    'limit' => 20
                ],
                $representation->getParameters()
            );
        }
    }

    public function testSerialize(){

        $factory = new KnpPaginatorFactory();

        $paginator = new Paginator;

        $paginate = $paginator->paginate(
            array(
                'mela',
                'meli',
                'melo'
            ), 1, 10
        );

        $collection = $factory->createRepresentation($paginate, new Route('my_route'));

        $this->assertJsonStringEqualsJsonString(
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
            "mela",
            "meli",
            "melo"
        ]
    }
}
JSON
        ,
            $this->json($this->hateoas->serialize($collection, 'json'))

        );


        $this->assertXmlStringEqualsXmlString(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="1" limit="10" pages="1" total="3">
  <entry rel="items">
    <entry><![CDATA[mela]]></entry>
    <entry><![CDATA[meli]]></entry>
    <entry><![CDATA[melo]]></entry>
  </entry>
  <link rel="self" href="my_route?page=1&amp;limit=10"/>
  <link rel="first" href="my_route?page=1&amp;limit=10"/>
  <link rel="last" href="my_route?page=1&amp;limit=10"/>
</collection>

XML
            ,
            $this->hateoas->serialize($collection, 'xml')
        );
    }
}
 
<?php

namespace Hateoas\Tests\Representation\Factory;

use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Tests\Representation\RepresentationTestCase;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\CallbackAdapter;
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

        $factory = new PagerfantaFactory('p', 'l');

        $representation = $factory->create(
            $pagerProphecy->reveal(),
            'users',
            array(
                'query' => 'hateoas',
            )
        );

        $this
            ->object($representation)
                ->isInstanceOf('Hateoas\Representation\PaginatedRepresentation')
            ->variable($representation->getPage())
                ->isEqualTo(2)
            ->variable($representation->getLimit())
                ->isEqualTo(20)
            ->variable($representation->getPages())
                ->isEqualTo(4)
            ->array($representation->getParameters())
                ->isEqualTo(array(
                    'query' => 'hateoas',
                    'p' => 2,
                    'l' => 20,
                ))
            ->string($representation->getPageParameterName())
                ->isEqualTo('p')
            ->string($representation->getLimitParameterName())
                ->isEqualTo('l')
        ;
    }

    public function testSerialize()
    {
        $factory    = new PagerfantaFactory();
        $pagerfanta = new Pagerfanta(new ArrayAdapter(array(
            'bim',
            'bam',
            'boom'
        )));

        $collection = $factory->create($pagerfanta, 'my_route');

        $this
            ->string($this->hateoas->serialize($collection, 'xml'))
            ->isEqualTo(
                <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="1" limit="10" pages="1">
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

        $collection = $factory->create(
            $pagerfanta,
            '/my_route',
            array(),
            true  // absolute
        );

        $this
            ->string($this->hateoas->serialize($collection, 'xml'))
            ->isEqualTo(
                <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="1" limit="10" pages="1">
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
            );
    }

    public function testArrayIteratorShouldBeSerialized()
    {
        $factory    = new PagerfantaFactory();
        $pagerfanta = new Pagerfanta(new CallbackAdapter(
            function () { return 2; },
            function () { return new \ArrayIterator(array('foo', 'bar')); }
        ));

        $collection = $factory->create(
            $pagerfanta,
            '/my_route',
            array(),
            true  // absolute
        );

        $this
            ->json($this->hateoas->serialize($collection, 'json'))
            ->isEqualTo(
                <<<JSON
{
    "page": 1,
    "limit": 10,
    "pages": 1,
    "_links": {
        "self": {
            "href": "http:\/\/example.com\/my_route?page=1&limit=10"
        },
        "first": {
            "href": "http:\/\/example.com\/my_route?page=1&limit=10"
        },
        "last": {
            "href": "http:\/\/example.com\/my_route?page=1&limit=10"
        }
    },
    "_embedded": {
        "items": [
            "foo",
            "bar"
        ]
    }
}
JSON
            );
    }
}

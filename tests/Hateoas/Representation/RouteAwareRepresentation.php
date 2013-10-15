<?php

namespace tests\Hateoas\Representation;

use Hateoas\HateoasBuilder;
use Hateoas\Representation\Collection as TestedCollection;
use Hateoas\Representation\RouteAwareRepresentation as TestedRouteAwareRepresentation;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use tests\TestCase;

class RouteAwareRepresentation extends TestCase
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
            ->addXmlHalSerializer()
            ->build()
        ;

        $collection = new TestedRouteAwareRepresentation(
            new TestedCollection(
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
            ->string($hateoas->serialize($collection, 'xml'))
            ->isEqualTo(
                <<<XML
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
            ->string($halHateoas->serialize($collection, 'xml'))
            ->isEqualTo(
                <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result href="/authors?query=willdurand%2FHateoas">
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
</result>

XML
            )
            ->string($halHateoas->serialize($collection, 'json'))
            ->isEqualTo(
                '{'
                    .'"_links":{'
                        .'"self":{'
                            .'"href":"\/authors?query=willdurand%2FHateoas"'
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
}

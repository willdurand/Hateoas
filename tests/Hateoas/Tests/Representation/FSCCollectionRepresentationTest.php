<?php

namespace Hateoas\Tests\Representation;

use Hateoas\Representation\FSCCollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

class FSCCollectionRepresentationTest extends RepresentationTestCase
{
    public function test()
    {
        $collection = new PaginatedRepresentation(
            new FSCCollectionRepresentation(
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
            17,
            null,
            null,
            false,
            100
        );


        $this
            ->string($this->halHateoas->serialize($collection, 'json'))
            ->isEqualTo(
                '{'
                    .'"results":['
                        .'"Adrien",'
                        .'"William"'
                    .'],'
                    .'"page":3,'
                    .'"limit":20,'
                    .'"pages":17,'
                    .'"total":100,'
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
                    .'}'
                .'}'
            )
        ;
    }
}

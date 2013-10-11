<?php

namespace tests\Hateoas\Representation\Factory;

use Hateoas\Representation\Factory\PagerfantaFactory as TestedPagerfantaFactory;
use tests\TestCase;

class PagerfantaFactory extends TestCase
{
    public function test()
    {
        $results = array(
            'Adrien',
            'William',
        );

        $this->mockGenerator->orphanize('__construct');
        $pager = new \mock\Pagerfanta\Pagerfanta();
        $pager->getMockController()->getCurrentPageResults = function () use ($results) {
            return $results;
        };
        $pager->getMockController()->getCurrentPage = function () use ($results) {
            return 2;
        };
        $pager->getMockController()->getMaxPerPage = function () use ($results) {
            return 20;
        };
        $pager->getMockController()->getNbPages = function () use ($results) {
            return 4;
        };

        $factory = new TestedPagerfantaFactory('p', 'l');
        $representation = $factory->create(
            $pager,
            'users',
            array(
                'query' => 'hateoas',
            )
        );

        $this
            ->object($representation)
                ->isInstanceOf('Hateoas\Representation\PaginatedCollection')
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
}

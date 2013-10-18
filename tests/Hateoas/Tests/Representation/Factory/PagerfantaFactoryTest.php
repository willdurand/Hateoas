<?php

namespace Hateoas\Tests\Representation\Factory;

use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Tests\TestCase;

class PagerfantaFactoryTest extends TestCase
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

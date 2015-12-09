<?php

namespace Hateoas\Tests\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Factory\LinksFactory;
use Hateoas\Model\Link;
use Hateoas\Tests\TestCase;
use JMS\Serializer\SerializationContext;

class LinksFactoryTest extends TestCase
{
    public function test()
    {
        $object = new \StdClass();
        $context = SerializationContext::create();

        $relations = array(
            new Relation('self', '/users/1'),
            new Relation('manager', '/users/2'),
        );
        $link = new Link('', '');

        $relationsRepositoryProphecy = $this->prophesize('Hateoas\Configuration\RelationsRepository');
        $relationsRepositoryProphecy
            ->getRelations($object)
            ->willReturn($relations)
            ->shouldBeCalledTimes(1)
        ;
        $linkFactoryProphecy = $this->prophesize('Hateoas\Factory\LinkFactory');
        $linkFactoryProphecy
            ->createLink($object, $relations[1])
            ->willReturn($link)
            ->shouldBeCalledTimes(1)
        ;
        $exclusionManagerProphecy = $this->prophesize('Hateoas\Serializer\ExclusionManager');
        $exclusionManagerProphecy
            ->shouldSkipLink($object, $relations[0], $context)
            ->willReturn(true)
            ->shouldBeCalledTimes(1)
        ;
        $exclusionManagerProphecy
            ->shouldSkipLink($object, $relations[1], $context)
            ->willReturn(false)
            ->shouldBeCalledTimes(1)
        ;

        $linksFactory = new LinksFactory(
            $relationsRepositoryProphecy->reveal(),
            $linkFactoryProphecy->reveal(),
            $exclusionManagerProphecy->reveal()
        );

        $links = $linksFactory->create($object, $context);

        $this->assertCount(1, $links);
        $this->assertContains($link, $links);
    }
}

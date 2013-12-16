<?php

namespace Hateoas\Tests\Factory;

use Hateoas\Configuration\Embedded;
use Hateoas\Configuration\Relation;
use Hateoas\Factory\EmbeddedsFactory;
use Hateoas\Tests\TestCase;

class EmbeddedsFactoryTest extends TestCase
{
    public function test()
    {
        $relations = array(
            new Relation('self', '/users/1'),
            new Relation('friend', '/users/42', 'expr(object.getFriend())'),
            new Relation('expr(object.getManagerRel())', '/users/42', new Embedded('expr(object.getManager())', 'expr(object.getXmlElementName())')),
        );
        $object = new \StdClass();
        $context = $this->prophesize('JMS\Serializer\SerializationContext')->reveal();

        $relationsRepositoryProphecy = $this->prophesize('Hateoas\Configuration\RelationsRepository');
        $relationsRepositoryProphecy
            ->getRelations($object)
            ->willReturn($relations)
            ->shouldBeCalledTimes(1)
        ;

        $ELProphecy = $this->prophesize('Hateoas\Expression\ExpressionEvaluator');
        $ELProphecy->evaluate('expr(object.getFriend())', $object)->willReturn(42)->shouldBeCalledTimes(1);
        $ELProphecy->evaluate('expr(object.getManager())', $object)->willReturn(42)->shouldBeCalledTimes(1);
        $ELProphecy->evaluate('expr(object.getManagerRel())', $object)->willReturn(42)->shouldBeCalledTimes(1);
        $ELProphecy->evaluate('expr(object.getXmlElementName())', $object)->willReturn(42)->shouldBeCalledTimes(1);
        $ELProphecy->evaluate($this->arg->any(), $object)->willReturnArgument();

        $exclusionManagerProphecy = $this->prophesize('Hateoas\Serializer\ExclusionManager');
        $exclusionManagerProphecy->shouldSkipEmbedded($object, $relations[0], $context)->willReturn(true);
        $exclusionManagerProphecy->shouldSkipEmbedded($object, $relations[1], $context)->willReturn(false);
        $exclusionManagerProphecy->shouldSkipEmbedded($object, $relations[2], $context)->willReturn(false);

        $embeddedsFactory = new EmbeddedsFactory(
            $relationsRepositoryProphecy->reveal(),
            $ELProphecy->reveal(),
            $exclusionManagerProphecy->reveal()
        );

        $embeddeds = $embeddedsFactory->create($object, $context);

        $this
            ->array($embeddeds)
                ->hasSize(2)
            ->object($embeddeds[0])
                ->isInstanceOf('Hateoas\Model\Embedded')
                ->variable($embeddeds[0]->getRel())
                    ->isEqualTo('friend')
                ->variable($embeddeds[0]->getData())
                    ->isEqualTo(42)
            ->object($embeddeds[1])
                ->isInstanceOf('Hateoas\Model\Embedded')
                ->variable($embeddeds[1]->getRel())
                    ->isEqualTo(42)
                ->variable($embeddeds[1]->getData())
                    ->isEqualTo(42)
                ->variable($embeddeds[1]->getXmlElementName())
                    ->isEqualTo(42)
        ;
    }
}

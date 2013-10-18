<?php

namespace Hateoas\Tests\Factory;

use Hateoas\Configuration\Embed;
use Hateoas\Configuration\Relation;
use Hateoas\Factory\EmbedsFactory;
use Hateoas\Tests\TestCase;

class EmbedsFactoryTest extends TestCase
{
    public function test()
    {
        $relations = array(
            new Relation('self', '/users/1'),
            new Relation('friend', '/users/42', 'expr(object.getFriend())'),
            new Relation('expr(object.getManagerRel())', '/users/42', new Embed('expr(object.getManager())', 'expr(object.getXmlElementName())')),
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
        $exclusionManagerProphecy->shouldSkipEmbed($object, $relations[0], $context)->willReturn(true);
        $exclusionManagerProphecy->shouldSkipEmbed($object, $relations[1], $context)->willReturn(false);
        $exclusionManagerProphecy->shouldSkipEmbed($object, $relations[2], $context)->willReturn(false);

        $embedsFactory = new EmbedsFactory(
            $relationsRepositoryProphecy->reveal(),
            $ELProphecy->reveal(),
            $exclusionManagerProphecy->reveal()
        );

        $embeds = $embedsFactory->create($object, $context);

        $this
            ->array($embeds)
                ->hasSize(2)
            ->object($embeds[0])
                ->isInstanceOf('Hateoas\Model\Embed')
                ->variable($embeds[0]->getRel())
                    ->isEqualTo('friend')
                ->variable($embeds[0]->getData())
                    ->isEqualTo(42)
            ->object($embeds[1])
                ->isInstanceOf('Hateoas\Model\Embed')
                ->variable($embeds[1]->getRel())
                    ->isEqualTo(42)
                ->variable($embeds[1]->getData())
                    ->isEqualTo(42)
                ->variable($embeds[1]->getXmlElementName())
                    ->isEqualTo(42)
        ;
    }
}

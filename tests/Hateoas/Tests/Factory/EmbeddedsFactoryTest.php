<?php

namespace Hateoas\Tests\Factory;

use Hateoas\Configuration\Embedded;
use Hateoas\Configuration\Relation;
use Hateoas\Factory\EmbeddedsFactory;
use Hateoas\Tests\TestCase;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\SerializationContext;
use Prophecy\Argument;

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
        $context = $this->prophesize(SerializationContext::class)->reveal();

        $relationsRepositoryProphecy = $this->prophesize('Hateoas\Configuration\RelationsRepository');
        $relationsRepositoryProphecy
            ->getRelations($object)
            ->willReturn($relations)
            ->shouldBeCalledTimes(1)
        ;
        $ctx = [
            'object' => $object,
            'context' => $context,
        ];

        $ELProphecy = $this->prophesize(ExpressionEvaluatorInterface::class);
        $ELProphecy->evaluate('object.getFriend()', $ctx)->willReturn(42)->shouldBeCalledTimes(1);
        $ELProphecy->evaluate('object.getManager()', $ctx)->willReturn(42)->shouldBeCalledTimes(1);
        $ELProphecy->evaluate('object.getManagerRel()', $ctx)->willReturn(42)->shouldBeCalledTimes(1);
        $ELProphecy->evaluate('object.getXmlElementName()', $ctx)->willReturn(42)->shouldBeCalledTimes(1);
        $ELProphecy->evaluate(Argument::any(), $ctx)->willReturnArgument();

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

        $this->assertCount(2, $embeddeds);
        $this->assertInstanceOf('Hateoas\Model\Embedded', $embeddeds[0]);
        $this->assertSame('friend', $embeddeds[0]->getRel());
        $this->assertSame(42, $embeddeds[0]->getData());
        $this->assertInstanceOf('Hateoas\Model\Embedded', $embeddeds[1]);
        $this->assertSame(42, $embeddeds[1]->getRel());
        $this->assertSame(42, $embeddeds[1]->getData());
        $this->assertSame(42, $embeddeds[1]->getXmlElementName());
    }
}

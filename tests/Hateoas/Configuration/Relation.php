<?php

namespace tests\Hateoas\Configuration;

use tests\TestCase;
use Hateoas\Configuration\Relation as TestedRelation;

class Relation extends TestCase
{
    public function testConstructor()
    {
        $relation = new TestedRelation('self', 'user_get');

        $this
            ->object($relation)
                ->isInstanceOf('Hateoas\Configuration\Relation')
            ->string($relation->getName())
                ->isEqualTo('self')
            ->string($relation->getHref())
                ->isEqualTo('user_get')
            ->array($relation->getAttributes())
                ->isEmpty()
        ;
    }

    public function requireHrefOrEmbed()
    {
        $this
            ->exception(function () {
                new Relation('', null, null);
            })
                ->isInstanceOf('InvalidArgumentException')
                    ->hasMessage('$href and $embed cannot be both null.')
        ;
    }

    public function canBeConstructedWithOnlyAnEmbed()
    {
        $relation = new TestedRelation('self', null, 'foo');

        $this
            ->object($relation)
                ->isInstanceOf('Hateoas\Configuration\Relation')
            ->string($relation->getName())
                ->isEqualTo('self')
            ->variable($relation->getHref())
                ->isNull()
            ->object($relation->getEmbed())
                ->isInstanceOf('Hateoas\Configuration\Embed')
            ->variable($relation->getEmbed()->getContent())
                ->isEqualTo('foo')
        ;
    }
}

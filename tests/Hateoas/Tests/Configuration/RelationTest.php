<?php

namespace Hateoas\Tests\Configuration;

use Hateoas\Tests\TestCase;
use Hateoas\Configuration\Relation;

class RelationTest extends TestCase
{
    public function testConstructor()
    {
        $relation = new Relation('self', 'user_get');

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
            ->hasMessage('$href and $embedded cannot be both null.')
        ;
    }

    public function canBeConstructedWithOnlyAnEmbed()
    {
        $relation = new Relation('self', null, 'foo');

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

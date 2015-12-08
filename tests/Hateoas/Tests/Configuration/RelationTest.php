<?php

namespace Hateoas\Tests\Configuration;

use Hateoas\Tests\TestCase;
use Hateoas\Configuration\Relation;

class RelationTest extends TestCase
{
    public function testConstructor()
    {
        $relation = new Relation('self', 'user_get');

        $this->assertSame('self', $relation->getName());
        $this->assertSame('user_get', $relation->getHref());
        $this->assertEmpty($relation->getAttributes());
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

        $this->assertSame('self', $relation->getName());
        $this->assertNull($relation->getHref());
        $this->assertEmpty($relation->getAttributes());
        $this->assertInstanceOf('Hateoas\Configuration\Embed', $relation->getEmbedded());
        $this->assertSame('foo', $relation->getEmbedded()->getContent());
    }
}

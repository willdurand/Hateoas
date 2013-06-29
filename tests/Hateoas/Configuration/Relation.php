<?php

namespace tests\Hateoas\Configuration;

use tests\Test;
use Hateoas\Configuration\Relation as TestedRelation;

class Relation extends Test
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
}

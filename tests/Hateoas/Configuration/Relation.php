<?php

namespace tests\Hateoas\Configuration;

use Hateoas\Configuration\Relation as RelationTested;
use tests\Test;

class Relation extends Test
{
    public function testConstructor()
    {
        $relation = new RelationTested('self', 'user_get');

        $this
            ->then()
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

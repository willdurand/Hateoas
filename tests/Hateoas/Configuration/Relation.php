<?php

namespace Hateoas\tests\units\Configuration;

use Hateoas\Configuration\Relation as RelationTested;
use mageekguy\atoum;

class Relation extends atoum\test
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

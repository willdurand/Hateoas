<?php

namespace tests\Hateoas\Serializer\Metadata;

use Hateoas\Configuration\Exclusion;
use Hateoas\Serializer\Metadata\RelationPropertyMetadata as TestedRelationPropertyMetadata;
use tests\TestCase;

class RelationPropertyMetadata extends TestCase
{
    public function test()
    {
        $propertyMetadata = new TestedRelationPropertyMetadata();

        $this
            ->variable($propertyMetadata->groups)
                ->isNull()
            ->variable($propertyMetadata->sinceVersion)
                ->isNull()
            ->variable($propertyMetadata->untilVersion)
                ->isNull()
            ->variable($propertyMetadata->maxDepth)
                ->isNull()
        ;
    }

    public function testWithExclusion()
    {
        $propertyMetadata = new TestedRelationPropertyMetadata(new Exclusion(
            array('foo', 'bar'),
            1.1,
            2.2,
            42
        ));

        $this
            ->variable($propertyMetadata->groups)
                ->isEqualTo(array('foo', 'bar'))
            ->variable($propertyMetadata->sinceVersion)
                ->isEqualTo(1.1)
            ->variable($propertyMetadata->untilVersion)
                ->isEqualTo(2.2)
            ->variable($propertyMetadata->maxDepth)
                ->isEqualTo(42)
        ;
    }
}

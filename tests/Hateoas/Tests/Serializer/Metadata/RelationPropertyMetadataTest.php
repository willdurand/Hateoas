<?php

namespace Hateoas\Tests\Serializer\Metadata;

use Hateoas\Configuration\Exclusion;
use Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use Hateoas\Tests\TestCase;

class RelationPropertyMetadataTest extends TestCase
{
    public function test()
    {
        $propertyMetadata = new RelationPropertyMetadata();

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
        $propertyMetadata = new RelationPropertyMetadata(new Exclusion(
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

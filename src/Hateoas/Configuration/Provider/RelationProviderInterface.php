<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider;

interface RelationProviderInterface
{
    /**
     * @return Relation[] Returns array of Relations for specified object.
     */
    public function getRelations(RelationProvider $relationProvider, string $class): array;
}

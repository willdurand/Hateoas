<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Metadata;

use Hateoas\Configuration\Relation;

interface ClassMetadataInterface
{
    public function getName(): string;

    /**
     * @return Relation[]
     */
    public function getRelations(): array;

    public function addRelation(Relation $relation): void;
}

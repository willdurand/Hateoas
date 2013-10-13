<?php

namespace Hateoas\Configuration\Metadata;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface ClassMetadataInterface
{
    /**
     * @return Relation[]
     */
    public function getRelations();

    /**
     * @return RelationProvider[]
     */
    public function getRelationProviders();
}

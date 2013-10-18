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
     * @return string
     */
    public function getName();

    /**
     * @return Relation[]
     */
    public function getRelations();

    /**
     * @return RelationProvider[]
     */
    public function getRelationProviders();

    /**
     * @param Relation $relation
     */
    public function addRelation(Relation $relation);

    /**
     * @param RelationProvider $relationProvider
     */
    public function addRelationProvider(RelationProvider $relationProvider);
}

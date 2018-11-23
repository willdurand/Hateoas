<?php

namespace Hateoas\Configuration\Metadata;

use Hateoas\Configuration\Relation;

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
     * @param Relation $relation
     */
    public function addRelation(Relation $relation);
}

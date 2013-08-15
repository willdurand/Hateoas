<?php

namespace Hateoas\Configuration\Metadata;

use Hateoas\Configuration\Relation;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface ClassMetadataInterface
{
    /**
     * @return Relation[]
     */
    public function getRelations();
}

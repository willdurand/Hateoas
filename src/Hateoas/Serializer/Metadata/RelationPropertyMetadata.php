<?php

namespace Hateoas\Serializer\Metadata;

use Hateoas\Configuration\Exclusion;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class RelationPropertyMetadata extends VirtualPropertyMetadata
{
    public function __construct(Exclusion $exclusion = null)
    {
        if (null === $exclusion) {
            return;
        }

        $this->groups = $exclusion->getGroups();
        $this->sinceVersion = $exclusion->getSinceVersion();
        $this->untilVersion = $exclusion->getUntilVersion();
        $this->maxDepth = $exclusion->getMaxDepth();
    }
}

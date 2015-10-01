<?php

namespace Hateoas\Serializer\Metadata;

use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Relation;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\TypeParser;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class RelationPropertyMetadata extends VirtualPropertyMetadata
{
    public function __construct(Exclusion $exclusion = null, Relation $relation = null)
    {
        if (null !== $relation) {
            $this->name = $relation->getName();
            $this->class = get_class($relation);

            $typeParser = new TypeParser();
            if (null !== $relation->getEmbedded()) {
                $this->type = $typeParser->parse('Hateoas\Model\Embedded');
            } elseif (null !== $relation->getHref()) {
                $this->type = $typeParser->parse('Hateoas\Model\Link');
            }
        }

        if (null === $exclusion) {
            return;
        }

        $this->groups = $exclusion->getGroups();
        $this->sinceVersion = $exclusion->getSinceVersion();
        $this->untilVersion = $exclusion->getUntilVersion();
        $this->maxDepth = $exclusion->getMaxDepth();
    }
}

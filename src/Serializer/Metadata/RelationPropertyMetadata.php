<?php

declare(strict_types=1);

namespace Hateoas\Serializer\Metadata;

use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Relation;
use JMS\Serializer\Expression\Expression;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;

class RelationPropertyMetadata extends VirtualPropertyMetadata
{
    public const EXPRESSION_REGEX = '/expr\((?P<expression>.+)\)/';

    public function __construct(?Exclusion $exclusion = null, ?Relation $relation = null)
    {
        if (null !== $relation) {
            $this->name = $relation->getName();
            $this->class = get_class($relation);

            if (null !== $relation->getEmbedded()) {
                $this->type = ['name' => 'Hateoas\Model\Embedded', 'params' => []];
            } elseif (null !== $relation->getHref()) {
                $this->type = ['name' => 'Hateoas\Model\Link', 'params' => []];
            }
        }

        if (null === $exclusion) {
            return;
        }

        $this->groups = $exclusion->getGroups();
        $this->sinceVersion = $exclusion->getSinceVersion();
        $this->untilVersion = $exclusion->getUntilVersion();
        $this->maxDepth = $exclusion->getMaxDepth();

        if ($exclusion->getExcludeIf() instanceof Expression) {
            $this->excludeIf = $exclusion->getExcludeIf();
        } elseif (null !== $exclusion->getExcludeIf()) {
            $this->excludeIf = $exclusion->getExcludeIf();
        }
    }
}

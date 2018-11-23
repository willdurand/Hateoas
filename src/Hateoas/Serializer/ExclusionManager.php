<?php

namespace Hateoas\Serializer;

use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Relation;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ExclusionManager
{
    /**
     * @var ExpressionLanguageExclusionStrategy
     */
    private $expressionExclusionStrategy;

    public function __construct(ExpressionLanguageExclusionStrategy $expressionLanguageExclusionStrategy)
    {
        $this->expressionExclusionStrategy = $expressionLanguageExclusionStrategy;
    }

    public function shouldSkipLink($object, Relation $relation, SerializationContext $context)
    {
        if ($this->shouldSkipRelation($relation, $context)) {
            return true;
        }

        if (null === $relation->getHref()) {
            return true;
        }

        return false;
    }

    public function shouldSkipEmbedded($object, Relation $relation, SerializationContext $context)
    {
        if (null === $relation->getEmbedded()) {
            return true;
        }

        if (null === $relation->getEmbedded()->getExclusion()) {
            return $this->shouldSkipRelation($relation, $context);
        }

        return $this->shouldSkip($relation, $relation->getEmbedded()->getExclusion(), $context);
    }

    private function shouldSkipRelation(Relation $relation, SerializationContext $context)
    {
        return $this->shouldSkip($relation, $relation->getExclusion(), $context);
    }

    private function shouldSkip(Relation $relation, Exclusion $exclusion = null, SerializationContext $context)
    {
        $propertyMetadata = new RelationPropertyMetadata($exclusion, $relation);
        if ($context->getExclusionStrategy()) {
            if ($context->getExclusionStrategy()->shouldSkipProperty($propertyMetadata, $context)) {
                return true;
            }
        }

        if (null !== $exclusion
            && null !== $exclusion->getExcludeIf()
            && $this->expressionExclusionStrategy->shouldSkipProperty($propertyMetadata, $context)
        ) {
            return true;
        }

        return false;
    }
}

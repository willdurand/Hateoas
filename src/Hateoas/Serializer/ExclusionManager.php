<?php

namespace Hateoas\Serializer;
use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Relation;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ExclusionManager
{
    /**
     * @var ExpressionEvaluator
     */
    private $expressionEvaluator;

    public function __construct(ExpressionEvaluator $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    public function shouldSkipLink($object, Relation $relation, SerializationContext $context)
    {
        if ($this->shouldSkipRelation($object, $relation, $context)) {
            return true;
        }

        if (null === $relation->getHref()) {
            return true;
        }

        return false;
    }

    public function shouldSkipEmbedded($object, Relation $relation, SerializationContext $context)
    {
        if ($this->shouldSkipRelation($object, $relation, $context)) {
            return true;
        }

        if (null === $relation->getEmbedded()) {
            return true;
        }

        return $this->shouldSkip($object, $relation->getEmbedded()->getExclusion(), $context);
    }

    private function shouldSkipRelation($object, Relation $relation, SerializationContext $context)
    {
        return $this->shouldSkip($object, $relation->getExclusion(), $context);
    }

    private function shouldSkip($object, Exclusion $exclusion = null, SerializationContext $context)
    {
        if (null !== $exclusion
            && null !== $exclusion->getExcludeIf()
            && $this->expressionEvaluator->evaluate($exclusion->getExcludeIf(), $object)
        ) {
            return true;
        }

        if (!$context->getExclusionStrategy()) {
            return false;
        }

        $propertyMetadata = new RelationPropertyMetadata($exclusion);

        return $context->getExclusionStrategy()->shouldSkipProperty($propertyMetadata, $context);
    }
}

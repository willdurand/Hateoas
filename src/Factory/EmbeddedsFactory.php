<?php

declare(strict_types=1);

namespace Hateoas\Factory;

use Hateoas\Model\Embedded;
use Hateoas\Serializer\ExclusionManager;
use Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use JMS\Serializer\Expression\Expression;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactoryInterface;

class EmbeddedsFactory
{
    /**
     * @var ExpressionEvaluatorInterface
     */
    private $expressionEvaluator;

    /**
     * @var ExclusionManager
     */
    private $exclusionManager;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        ExpressionEvaluatorInterface $expressionEvaluator,
        ExclusionManager $exclusionManager
    ) {
        $this->expressionEvaluator = $expressionEvaluator;
        $this->exclusionManager = $exclusionManager;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @return Embedded[]
     */
    public function create(object $object, SerializationContext $context): array
    {
        $embeddeds = [];

        if (null !== ($classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object)))) {
            $langugeData = ['object' => $object, 'context' => $context];
            foreach ($classMetadata->getRelations() as $relation) {
                if ($this->exclusionManager->shouldSkipEmbedded($object, $relation, $context)) {
                    continue;
                }

                $rel = $relation->getName();
                $data = $this->checkExpression($relation->getEmbedded()->getContent(), $langugeData);
                $xmlElementName = $this->checkExpression($relation->getEmbedded()->getXmlElementName(), $langugeData);

                $propertyMetadata = new RelationPropertyMetadata($relation->getEmbedded()->getExclusion(), $relation);

                $embeddeds[] = new Embedded($rel, $data, $propertyMetadata, $xmlElementName, $relation->getEmbedded()->getType());
            }
        }

        return $embeddeds;
    }

    /**
     * @param mixed $exp
     *
     * @return mixed
     */
    private function checkExpression($exp, array $data)
    {
        if ($exp instanceof Expression) {
            return $this->expressionEvaluator->evaluate((string) $exp, $data);
        } else {
            return $exp;
        }
    }
}

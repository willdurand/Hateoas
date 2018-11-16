<?php

namespace Hateoas\Factory;

use Hateoas\Model\Embedded;
use Hateoas\Serializer\ExclusionManager;
use Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactoryInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
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
     * @param  object $object
     * @param  SerializationContext $context
     * @return Embedded[]
     */
    public function create($object, SerializationContext $context)
    {
        $embeddeds = array();

        if (null !== ($classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object)))) {

            $langugeData = ['object' => $object, 'context' => $context];
            foreach ($classMetadata->getRelations() as $relation) {
                if ($this->exclusionManager->shouldSkipEmbedded($object, $relation, $context)) {
                    continue;
                }

                $rel = $this->checkExpression($relation->getName(), $langugeData);
                $data = $this->checkExpression($relation->getEmbedded()->getContent(), $langugeData);
                $xmlElementName = $this->checkExpression($relation->getEmbedded()->getXmlElementName(), $langugeData);

                $propertyMetadata = new RelationPropertyMetadata($relation->getEmbedded()->getExclusion(), $relation);

                $embeddeds[] = new Embedded($rel, $data, $propertyMetadata, $xmlElementName);
            }
        }
        return $embeddeds;
    }

    const EXPRESSION_REGEX = '/expr\((?P<expression>.+)\)/';

    private function checkExpression($exp, array $data)
    {
        if (is_string($exp) && preg_match(self::EXPRESSION_REGEX, $exp, $matches)) {
            return $this->expressionEvaluator->evaluate($matches['expression'], $data);
        } else {
            return $exp;
        }
    }
}

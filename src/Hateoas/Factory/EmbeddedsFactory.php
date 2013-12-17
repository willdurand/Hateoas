<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\RelationsRepository;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Model\Embedded;
use Hateoas\Serializer\ExclusionManager;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class EmbeddedsFactory
{
    /**
     * @var RelationsRepository
     */
    private $relationsRepository;

    /**
     * @var ExpressionEvaluator
     */
    private $expressionEvaluator;

    /**
     * @var ExclusionManager
     */
    private $exclusionManager;

    /**
     * @param RelationsRepository $relationsRepository
     * @param ExpressionEvaluator $expressionEvaluator
     * @param ExclusionManager    $exclusionManager
     */
    public function __construct(
        RelationsRepository $relationsRepository,
        ExpressionEvaluator $expressionEvaluator,
        ExclusionManager $exclusionManager
    ) {
        $this->relationsRepository = $relationsRepository;
        $this->expressionEvaluator = $expressionEvaluator;
        $this->exclusionManager    = $exclusionManager;
    }
    /**
     * @param  object               $object
     * @param  SerializationContext $context
     * @return Embedded[]
     */
    public function create($object, SerializationContext $context)
    {
        $embeddeds = array();
        foreach ($this->relationsRepository->getRelations($object) as $relation) {
            if ($this->exclusionManager->shouldSkipEmbedded($object, $relation, $context)) {
                continue;
            }

            $rel  = $this->expressionEvaluator->evaluate($relation->getName(), $object);
            $data = $this->expressionEvaluator->evaluate($relation->getEmbedded()->getContent(), $object);
            $xmlElementName = $this->expressionEvaluator->evaluate($relation->getEmbedded()->getXmlElementName(), $object);

            $embeddeds[] = new Embedded($rel, $data, $xmlElementName);
        }

        return $embeddeds;
    }
}

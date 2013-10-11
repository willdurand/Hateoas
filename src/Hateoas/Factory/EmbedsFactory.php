<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\RelationsRepository;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Model\Embed;
use Hateoas\Serializer\ExclusionManager;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class EmbedsFactory
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
     * @return Embed[]
     */
    public function create($object, SerializationContext $context)
    {
        $embeds = array();
        foreach ($this->relationsRepository->getRelations($object) as $relation) {
            if ($this->exclusionManager->shouldSkipEmbed($object, $relation, $context)) {
                continue;
            }

            $rel  = $this->expressionEvaluator->evaluate($relation->getName(), $object);
            $data = $this->expressionEvaluator->evaluate($relation->getEmbed()->getContent(), $object);
            $xmlElementName = $this->expressionEvaluator->evaluate($relation->getEmbed()->getXmlElementName(), $object);

            $embeds[] = new Embed($rel, $data, $xmlElementName);
        }

        return $embeds;
    }
}

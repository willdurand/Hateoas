<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\RelationsRepository;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Model\Embed;

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
     * @param RelationsRepository $relationsRepository
     * @param ExpressionEvaluator $expressionEvaluator
     */
    public function __construct(
        RelationsRepository $relationsRepository,
        ExpressionEvaluator $expressionEvaluator
    )
    {
        $this->relationsRepository = $relationsRepository;
        $this->expressionEvaluator      = $expressionEvaluator;
    }
    /**
     * @param  object  $object
     * @return Embed[]
     */
    public function create($object)
    {
        $embeds = array();
        foreach ($this->relationsRepository->getRelations($object) as $relation) {
            if (null === $relation->getEmbed()) {
                continue;
            }

            $rel  = $this->expressionEvaluator->evaluate($relation->getName(), $object);
            $data = $this->expressionEvaluator->evaluate($relation->getEmbed()->getContent(), $object);

            $embeds[] = new Embed($rel, $data, $relation->getEmbed()->getXmlElementName());
        }

        return $embeds;
    }
}

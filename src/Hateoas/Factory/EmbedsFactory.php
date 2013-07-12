<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\RelationsRepository;
use Hateoas\Handler\HandlerManager;
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
     * @var HandlerManager
     */
    private $handlerManager;

    public function __construct(RelationsRepository $relationsRepository, HandlerManager $handlerManager)
    {
        $this->relationsRepository = $relationsRepository;
        $this->handlerManager = $handlerManager;
    }
    /**
     * @param  object  $object
     * @return Embed[]
     */
    public function create($object)
    {
        $embeds = array();

        $relations = $this->relationsRepository->getRelations($object);
        foreach ($relations as $relation) {
            if (null === $relation->getEmbed()) {
                continue;
            }

            $rel = $this->handlerManager->transform($relation->getName(), $object);
            $data = $this->handlerManager->transform($relation->getEmbed(), $object);
            $embeds[] = new Embed($rel, $data);
        }

        return $embeds;
    }
}

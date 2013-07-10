<?php

namespace Hateoas\Factory;
use Hateoas\Configuration\RelationsRepository;
use Hateoas\Handler\HandlerManager;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class EmbeddedMapFactory
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
     * @param  object        $object
     * @return array<string, mixed> rel => data
     */
    public function create($object)
    {
        $embeddedMap = array();

        $relations = $this->relationsRepository->getRelations($object);
        foreach ($relations as $relation) {
            if (null === $relation->getEmbed()) {
                continue;
            }

            $embeddedMap[$relation->getName()] = $this->handlerManager->transform($relation->getEmbed(), $object);
        }

        return $embeddedMap;
    }
}

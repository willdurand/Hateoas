<?php

namespace Hateoas\Factory;
use Hateoas\Configuration\RelationsManagerInterface;
use Hateoas\Handler\HandlerManager;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class EmbeddedMapFactory
{
    /**
     * @var RelationsManagerInterface
     */
    private $relationsManager;

    /**
     * @var HandlerManager
     */
    private $handlerManager;

    public function __construct(RelationsManagerInterface $relationsManager, HandlerManager $handlerManager)
    {
        $this->relationsManager = $relationsManager;
        $this->handlerManager = $handlerManager;
    }
    /**
     * @param object $object
     * @return \SplObjectStorage Map<Relation, mixed>
     */
    public function create($object)
    {
        $embeddedMap = new \SplObjectStorage();

        $relations = $this->relationsManager->getRelations($object);
        foreach ($relations as $relation) {
            if (null === $relation->getEmbed()) {
                continue;
            }

            $embeddedMap->attach(
                $relation,
                $this->handlerManager->transform($relation->getEmbed(), $object)
            );
        }

        return $embeddedMap;
    }
}

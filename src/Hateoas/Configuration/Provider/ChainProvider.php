<?php

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ChainProvider implements RelationProviderInterface
{
    /**
     * @var RelationProviderInterface[]
     */
    private $resolvers;

    public function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    public function addProvider(RelationProviderInterface $resolver)
    {
        $this->resolvers[] = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelations(RelationProvider $configuration, string $class):array
    {
        $relations = [];
        foreach ($this->resolvers as $resolver) {
            $relations = array_merge($relations, $resolver->getRelations($configuration, $class));
        }

        return $relations;
    }
}

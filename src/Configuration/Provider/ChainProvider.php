<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider;

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

    public function addProvider(RelationProviderInterface $resolver): void
    {
        $this->resolvers[] = $resolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getRelations(RelationProvider $configuration, string $class): array
    {
        $relations = [];
        foreach ($this->resolvers as $resolver) {
            $relations = array_merge($relations, $resolver->getRelations($configuration, $class));
        }

        return $relations;
    }
}

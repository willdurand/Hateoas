<?php

namespace Hateoas\Configuration\Provider\Resolver;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ChainResolver implements RelationProviderResolverInterface
{
    /**
     * @var RelationProviderResolverInterface[]
     */
    private $resolvers;

    public function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    public function addResolver(RelationProviderResolverInterface $resolver)
    {
        $this->resolvers[] = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationProvider(RelationProviderConfiguration $configuration, $object)
    {
        $configurationCallable = null;
        foreach ($this->resolvers as $resolver) {
            $configurationCallable = $resolver->getRelationProvider($configuration, $object);

            if (null !== $configurationCallable) {
                break;
            }
        }

        return $configurationCallable;
    }
}

<?php

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ProviderChain implements RelationProviderProviderInterface
{
    /**
     * @var RelationProviderProviderInterface[]
     */
    private $providers = array();

    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    public function addProvider(RelationProviderProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function get(RelationProviderConfiguration $relationProvider, $object)
    {
        $relationProviderCallable = null;

        foreach ($this->providers as $provider) {
            $relationProviderCallable = $provider->get($relationProvider, $object);

            if (null !== $relationProviderCallable) {
                break;
            }
        }

        return $relationProviderCallable;
    }
}

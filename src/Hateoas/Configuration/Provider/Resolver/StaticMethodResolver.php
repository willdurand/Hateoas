<?php

namespace Hateoas\Configuration\Provider\Resolver;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class StaticMethodResolver implements RelationProviderResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRelationProvider(RelationProviderConfiguration $configuration, $object)
    {
        if (!preg_match('/^(?P<class>[a-z0-9_\\\\]+)::(?P<method>[a-z0-9_]+)$/i', $configuration->getName(), $matches)) {
            return null;
        }

        return array($matches['class'], $matches['method']);
    }
}

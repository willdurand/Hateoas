<?php

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class StaticMethodRelationProviderProvider implements RelationProviderProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(RelationProviderConfiguration $relationProvider, $object)
    {
        if (!preg_match('/^(?P<class>[a-z0-9_\\\\]+)::(?P<method>[a-z0-9_]+)$/i', $relationProvider->getName(), $matches)) {
            return null;
        }

        return array($matches['class'], $matches['method']);
    }
}

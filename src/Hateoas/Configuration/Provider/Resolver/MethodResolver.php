<?php

namespace Hateoas\Configuration\Provider\Resolver;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class MethodResolver implements RelationProviderResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRelationProvider(RelationProviderConfiguration $configuration, $object)
    {
        if (!preg_match('/^[a-z0-9_]+$/i', $configuration->getName(), $matches)) {
            return null;
        }

        return array($object, $matches[0]);
    }
}

<?php

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class MethodRelationProviderProvider implements RelationProviderProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(RelationProviderConfiguration $relationProvider, $object)
    {
        if (!preg_match('/^[a-z0-9_]+$/i', $relationProvider->getName(), $matches)) {
            return null;
        }

        return array($object, $matches[0]);
    }
}

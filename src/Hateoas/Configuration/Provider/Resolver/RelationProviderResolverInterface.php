<?php

namespace Hateoas\Configuration\Provider\Resolver;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface RelationProviderResolverInterface
{
    /**
     * @param  RelationProviderConfiguration $relationProvider
     * @param  object                        $object
     * @return callable|false                Returns `false` if it does not support this RelationProvider,
     *                                       a `callable` otherwise.
     */
    public function getRelationProvider(RelationProviderConfiguration $configuration, $object);
}

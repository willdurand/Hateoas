<?php

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface RelationProviderProviderInterface
{
    /**
     * @param  RelationProviderConfiguration $relationProvider
     * @param  object                        $object
     * @return callable|null                                   Return null if it does not support this RelationProvider
     */
    public function get(RelationProviderConfiguration $relationProvider, $object);
}

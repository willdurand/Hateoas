<?php

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider;

/**
 * @author Vyacheslav Salakhutdinov <megazoll@gmail.com>
 */
interface RelationProviderInterface
{
    /**
     * @param RelationProvider $relationProvider
     * @param string $class
     * @return \Hateoas\Configuration\Relation[] Returns array of Relations for specified object.
     */
    public function getRelations(RelationProvider $relationProvider, string $class): array;
}

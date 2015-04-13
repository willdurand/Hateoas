<?php

namespace Hateoas\Configuration\Provider;

/**
 * @author Vyacheslav Salakhutdinov <megazoll@gmail.com>
 */
interface RelationProviderInterface
{
    /**
     * @param  object                            $object
     * @return \Hateoas\Configuration\Relation[] Returns array of Relations for specified object.
     */
    public function getRelations($object);
}

<?php

namespace Hateoas\Configuration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface RelationsManagerInterface
{
    /**
     * @param $object
     * @return Relation[]
     */
    public function getRelations($object);

    /**
     * @param $object
     * @param  Relation $relation
     * @return void
     */
    public function addRelation($object, Relation $relation);

    /**
     * @param $class
     * @param  Relation $relation
     * @return void
     */
    public function addClassRelation($class, Relation $relation);
}

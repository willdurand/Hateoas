<?php

namespace Hateoas\Builder;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
interface ResourceBuilderInterface
{
    /**
     * @param  object   $data
     * @return Resource
     */
    public function create($data);

    /**
     * @param array|\Traversable $collection
     * @param  string     $className
     * @return Collection
     */
    public function createCollection($collection, $className);
}

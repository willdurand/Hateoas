<?php

namespace Hateoas\Factory;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
interface FactoryInterface
{
    public function getResourceDefinition($data);

    public function getCollectionDefinition($data);
}

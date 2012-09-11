<?php

namespace Hateoas\Factory\Definition;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class CollectionDefinition extends ResourceDefinition
{
    public function __construct($class, array $links = array())
    {
        parent::__construct($class, $links);
    }
}

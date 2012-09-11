<?php

namespace Hateoas\Factory\Definition;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class CollectionDefinition extends ResourceDefinition
{
    private $attributes;

    public function __construct($class, array $links = array(), $attributes = array())
    {
        parent::__construct($class, $links);

        $this->attributes = $attributes;
    }

    public function getTotal()
    {
        return $this->getAttribute('total');
    }

    public function getLimit()
    {
        return $this->getAttribute('limit');
    }

    public function getPage()
    {
        return $this->getAttribute('page');
    }

    private function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }
}

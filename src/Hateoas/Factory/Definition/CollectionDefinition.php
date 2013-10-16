<?php

namespace Hateoas\Factory\Definition;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class CollectionDefinition extends ResourceDefinition
{
    private $attributes;

    private $rootName;

    public function __construct($class, array $links = array(), $attributes = array(), $rootName = null)
    {
        parent::__construct($class, $links);

        $this->attributes = $attributes;
        $this->rootName   = $rootName;
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

    public function getOffset()
    {
        return $this->getAttribute('offset');
    }

    public function getRootName()
    {
        return $this->rootName;
    }

    private function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }
}

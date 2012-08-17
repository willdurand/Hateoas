<?php

namespace Hateoas\Factory\Definition;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class ResourceDefinition
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var array
     */
    private $links;

    public function __construct($class, array $links = array())
    {
        $this->class = $class;
        $this->links = $links;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }
}

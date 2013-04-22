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
    
    /**
     * @var array
     */
    private $embedded;

    public function __construct($class, array $links = array(), array $embedded = array())
    {
        $this->class = $class;
        $this->links = $links;
        $this->embedded = $embedded;
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
    
    /**
     * @return array
     */
    public function getEmbedded()
    {
        return $this->embedded;
    }
}

<?php

namespace Hateoas;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Collection
{
    /**
     * @var array
     */
    private $resources;

    /**
     * @var array
     */
    private $links;

    /**
     * @var int
     */
    private $total;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    public function __construct(array $resources = array(), array $links = array())
    {
        $this->resources = $resources;
        $this->links     = $links;
    }

    /**
     * @return array
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }
}

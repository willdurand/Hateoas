<?php

namespace Hateoas;

use JMS\Serializer\Annotation\Inline;
use JMS\Serializer\Annotation\SerializedName;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Collection
{
    /**
     * @Inline
     * @var array
     */
    private $resources;

    /**
     * @SerializedName("_links")
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

    public function __construct(array $resources = array(), array $links = array(), $total = null, $page = null, $limit = null)
    {
        $this->resources = $resources;
        $this->links     = $links;
        $this->total     = $total;
        $this->page      = $page;
        $this->limit     = $limit;
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

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
}

<?php

namespace Hateoas;

use JMS\Serializer\Annotation\Inline;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlList;

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
     * @XmlList(entry = "link", inline = true)
     * @var array
     */
    private $links;

    /**
     * @XmlAttribute
     * @var int
     */
    private $total;

    /**
     * @XmlAttribute
     * @var int
     */
    private $page;

    /**
     * @XmlAttribute
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

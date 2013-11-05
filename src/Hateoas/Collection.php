<?php

namespace Hateoas;

use JMS\Serializer\Annotation\SerializedName;

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
     * @var array
     */
    private $forms;

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

    /**
     * @var string
     */
    private $rootName;

    /**
     * @var int
     */
    private $offset;
    /**
     * @var int
     */
    private $count;

    public function __construct($rootName = null, array $resources = array(), array $links = array(), $total = null, $page = null, $limit = null, array $forms = array(), $offset = null, $count = null)
    {
        $this->rootName  = $rootName;
        $this->resources = $resources;
        $this->links     = $links;
        $this->total     = $total;
        $this->page      = $page;
        $this->limit     = $limit;
        $this->forms     = $forms;
        $this->offset    = $offset;
        $this->count    = $count;
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

    /**
     * @return string
     */
    public function getRootName()
    {
        return $this->rootName;
    }

    /**
     * @return array
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * @param array $forms
     */
    public function setForms(array $forms)
    {
        $this->forms = $forms;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }
    
    /**
     * @return int
     */
    public function getCount()
    {
    	return $this->count;
    }
}

<?php

namespace Hateoas;

use JMS\Serializer\Annotation\SerializedName;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Resource
{
    /**
     * @var object|array
     */
    private $data;

    /**
     * @SerializedName("_links")
     * @var array
     */
    private $links;
    
    /**
     * @SerializedName("_embedded")
     * @var array
     */
    private $embedded;

    public function __construct($data, $links = array(), $embedded = array())
    {
        $this->data  = $data;
        $this->links = $links;
        $this->embedded = $embedded;
    }

    /**
     * @return object|array
     */
    public function getData()
    {
        return $this->data;
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

    /**
     * @param  Link     $link
     * @return Resource
     */
    public function addLink(Link $link)
    {
        if (!in_array($link, $this->links)) {
            $this->links[] = $link;
        }

        return $this;
    }
}

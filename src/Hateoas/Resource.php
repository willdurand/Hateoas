<?php

namespace Hateoas;

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
     * @var array
     */
    private $links;

    public function __construct($data, $links = array())
    {
        $this->data  = $data;
        $this->links = $links;
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

<?php

namespace Hateoas\Model;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Resource
{
    /**
     * @var array<string, mixed>
     */
    private $data;

    /**
     * @var array<string, mixed>
     */
    private $embedded;

    /**
     * @var Link[]
     */
    private $links;

    /**
     * @var null|string
     */
    private $xmlRootName;

    public function __construct(array $data, array $links, array $embedded = array(), $xmlRootName = null)
    {
        $this->data = $data;
        $this->embedded = $embedded;
        $this->links = $links;
        $this->xmlRootName = $xmlRootName;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getEmbedded()
    {
        return $this->embedded;
    }

    /**
     * @return Link[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @return null|string
     */
    public function getXmlRootName()
    {
        return $this->xmlRootName;
    }
}

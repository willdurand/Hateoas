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
     * @var Link[]
     */
    private $links;

    /**
     * @var Embed[]
     */
    private $embeds;

    /**
     * @var null|string
     */
    private $xmlRootName;

    public function __construct(array $data, array $links, array $embeds = array(), $xmlRootName = null)
    {
        $this->data = $data;
        $this->links = $links;
        $this->embeds = $embeds;
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
     * @return Embed[]
     */
    public function getEmbeds()
    {
        return $this->embeds;
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

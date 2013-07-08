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

    function __construct(array $data, array $links, array $embedded = array())
    {
        $this->data = $data;
        $this->embedded = $embedded;
        $this->links = $links;
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
}

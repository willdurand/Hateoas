<?php

namespace Hateoas\Model;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Link
{
    /**
     * @var string
     */
    private $rel;

    /**
     * @var string
     */
    private $href;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @param string $rel
     * @param string $href
     * @param array  $attributes
     */
    public function __construct($rel, $href, array $attributes = array())
    {
        $this->rel        = $rel;
        $this->href       = $href;
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }
}

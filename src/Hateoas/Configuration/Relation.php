<?php

namespace Hateoas\Configuration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Relation
{
    /**
     * @var string The link "rel" attribute
     */
    private $name;

    /**
     * @var string|Route
     */
    private $href;

    /**
     * @var array Extra link attributes
     */
    private $attributes;

    /**
     * @var string|mixed
     */
    private $embed;

    public function __construct($name, $href, $embed = null, array $attributes = array())
    {
        $this->name = $name;
        $this->href = $href;
        $this->embed = $embed;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Route|string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return mixed|string
     */
    public function getEmbed()
    {
        return $this->embed;
    }
}

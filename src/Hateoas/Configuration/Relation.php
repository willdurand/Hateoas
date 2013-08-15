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
     * @var Embed
     */
    private $embed;

    /**
     * @param string             $name
     * @param string|Route       $href
     * @param Embed|string|mixed $embed
     * @param array              $attributes
     */
    public function __construct($name, $href, $embed = null, array $attributes = array())
    {
        if (null !== $embed && !$embed instanceof Embed) {
            $embed = new Embed($embed);
        }

        $this->name       = $name;
        $this->href       = $href;
        $this->embed      = $embed;
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
     * @return Embed
     */
    public function getEmbed()
    {
        return $this->embed;
    }
}

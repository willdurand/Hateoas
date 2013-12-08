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
     * @var string|Route|null
     */
    private $href;

    /**
     * @var array Extra link attributes
     */
    private $attributes;

    /**
     * @var Embed|null
     */
    private $embed;

    /**
     * @var Exclusion|null
     */
    private $exclusion;

    /**
     * @param string             $name
     * @param string|Route       $href
     * @param Embed|string|mixed $embed
     * @param array              $attributes
     * @param Exclusion          $exclusion
     */
    public function __construct($name, $href = null, $embed = null, array $attributes = array(), Exclusion $exclusion = null)
    {
        if (null !== $embed && !$embed instanceof Embed) {
            $embed = new Embed($embed);
        }

        if (null === !$href && null === $embed) {
            throw new \InvalidArgumentException('$href and $embed cannot be both null.');
        }

        $this->name       = $name;
        $this->href       = $href;
        $this->embed      = $embed;
        $this->attributes = $attributes;
        $this->exclusion  = $exclusion;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Route|string|null
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
     * @return Embed|null
     */
    public function getEmbed()
    {
        return $this->embed;
    }

    /**
     * @return Exclusion|null
     */
    public function getExclusion()
    {
        return $this->exclusion;
    }
}

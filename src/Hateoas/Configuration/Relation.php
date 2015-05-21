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
     * @var Embedded|null
     */
    private $embedded;

    /**
     * @var Exclusion|null
     */
    private $exclusion;

    /**
     * @param string                $name
     * @param string|Route          $href
     * @param Embedded|string|mixed $embedded
     * @param array                 $attributes
     * @param Exclusion             $exclusion
     */
    public function __construct($name, $href = null, $embedded = null, array $attributes = array(), Exclusion $exclusion = null)
    {
        if (null !== $embedded && !$embedded instanceof Embedded) {
            $embedded = new Embedded($embedded);
        }

        if (null === !$href && null === $embedded) {
            throw new \InvalidArgumentException('$href and $embedded cannot be both null.');
        }

        $this->name       = $name;
        $this->href       = $href;
        $this->embedded   = $embedded;
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
     * @return Embedded|null
     */
    public function getEmbedded()
    {
        return $this->embedded;
    }

    /**
     * @return Exclusion|null
     */
    public function getExclusion()
    {
        return $this->exclusion;
    }
}

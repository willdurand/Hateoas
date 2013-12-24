<?php

namespace Hateoas\Configuration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string|array
     */
    private $parameters;

    /**
     * @var boolean
     */
    private $absolute;

    /**
     * @var null|string
     */
    private $generator;

    /**
     * @param string       $name
     * @param string|array $parameters
     * @param boolean      $absolute
     * @param string|null  $generator
     */
    public function __construct($name, $parameters = array(), $absolute = false, $generator = null)
    {
        $this->name       = $name;
        $this->parameters = $parameters;
        $this->absolute   = $absolute;
        $this->generator  = $generator;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return boolean
     */
    public function isAbsolute()
    {
        return $this->absolute;
    }

    /**
     * @return null|string
     */
    public function getGenerator()
    {
        return $this->generator;
    }
}

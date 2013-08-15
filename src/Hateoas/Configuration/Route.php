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
     * @var array
     */
    private $parameters;

    /**
     * @var boolean
     */
    private $isAbsolute;

    /**
     * @param string  $name
     * @param array   $parameters
     * @param boolean $isAbsolute
     */
    public function __construct($name, array $parameters = array(), $isAbsolute = false)
    {
        $this->name       = $name;
        $this->parameters = $parameters;
        $this->isAbsolute = $isAbsolute;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
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
        return $this->isAbsolute;
    }
}

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
     * @var null|string
     */
    private $generator;

    /**
     * @param string      $name
     * @param array       $parameters
     * @param boolean     $isAbsolute
     * @param string|null $generatorName
     */
    public function __construct(
        $name,
        array $parameters = array(),
        $isAbsolute = false,
        $generatorName = null
    ) {
        $this->name        = $name;
        $this->parameters  = $parameters;
        $this->isAbsolute  = $isAbsolute;
        $this->generator   = $generatorName;
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

    /**
     * @return null|string
     */
    public function getGenerator()
    {
        return $this->generator;
    }
}

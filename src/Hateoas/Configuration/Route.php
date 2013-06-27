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

    public function __construct($name, $parameters = array())
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }
}

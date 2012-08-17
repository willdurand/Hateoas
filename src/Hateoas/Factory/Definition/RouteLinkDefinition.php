<?php

namespace Hateoas\Factory\Definition;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class RouteLinkDefinition extends LinkDefinition
{
    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $parameters;

    public function __construct($route, array $parameters, $rel, $type = null)
    {
        parent::__construct($rel, $type);

        $this->route = $route;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}

<?php

namespace Hateoas\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters())"
 *      )
 * )
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class RouteAwareRepresentation
{
    /**
     * @Serializer\Inline
     * @Serializer\Expose
     */
    private $inline;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $parameters;

    public function __construct($inline, $route, array $parameters = array())
    {
        $this->inline = $inline;
        $this->route = $route;
        $this->parameters = $parameters;
    }

    /**
     * @return mixed
     */
    public function getInline()
    {
        return $this->inline;
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

<?php

namespace Hateoas\Factory;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface RouteFactoryInterface
{
    /**
     * @param  string  $name
     * @param  array   $parameters
     * @param  boolean $absolute
     * @return string
     */
    public function create($name, array $parameters, $absolute = false);
}

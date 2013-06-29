<?php

namespace Hateoas\Factory;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface RouteFactoryInterface
{
    /**
     * @param $name
     * @param $parameters
     * @return string
     */
    public function create($name, $parameters);
}

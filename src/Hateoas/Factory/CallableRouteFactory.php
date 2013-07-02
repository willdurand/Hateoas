<?php

namespace Hateoas\Factory;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class CallableRouteFactory implements RouteFactoryInterface
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, array $parameters, $absolute = false)
    {
        return call_user_func_array($this->callable, array($name, $parameters, $absolute));
    }
}

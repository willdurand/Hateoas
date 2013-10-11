<?php

namespace Hateoas\UrlGenerator;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class CallableUrlGenerator implements UrlGeneratorInterface
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
    public function generate($name, array $parameters, $absolute = false)
    {
        return call_user_func_array($this->callable, array($name, $parameters, $absolute));
    }
}

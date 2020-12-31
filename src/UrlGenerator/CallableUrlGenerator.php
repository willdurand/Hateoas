<?php

declare(strict_types=1);

namespace Hateoas\UrlGenerator;

class CallableUrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param bool|int $absolute
     */
    public function generate(string $name, array $parameters, $absolute = false): string
    {
        return call_user_func_array($this->callable, [$name, $parameters, $absolute]);
    }
}

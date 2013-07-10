<?php

namespace Hateoas\Handler;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 *
 * TODO rename the $value var that is everywhere, to something more meaningful ...
 */
class HandlerManager
{
    /**
     * @var HandlerInterface[] name => HandlerInterface
     */
    private $handlers;

    public function __construct(array $handlers = array())
    {
        foreach ($handlers as $name => $handler) {
            $this->setHandler($name, $handler);
        }
    }

    public function setHandler($name, HandlerInterface $handler)
    {
        $this->handlers[$name] = $handler;
    }

    public function transform($value, $object)
    {
        if (!is_string($value) || 0 !== strpos($value, '@')) {
            return $value;
        }

        if (!preg_match('/^@([a-zA-Z0-9]+)[.](.+)$/', $value, $matches)) {
            throw new \InvalidArgumentException(sprintf('Cannot parse "%s".', $value));
        }

        $handlerName = $matches[1];
        $handlerValue = $matches[2];

        if (!isset($this->handlers[$handlerName])) {
            throw new \InvalidArgumentException(sprintf('Handler "%s" does not exist.', $handlerName));
        }

        $handler = $this->handlers[$handlerName];

        return $handler->transform($handlerValue, $object);
    }

    public function transformArray(array $array, $object)
    {
        $newArray = array();

        foreach ($array as $key => $value) {
            $key = $this->transform($key, $object);
            $value = $this->transform($value, $object);

            $newArray[$key] = $value;
        }

        return $newArray;
    }
}

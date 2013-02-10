<?php

namespace Hateoas\Builder;

use Hateoas\Link;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class CallableLinkBuilder extends RouteAwareLinkBuilder
{
    private $callable;

    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    /**
     * {@inheritDoc}
     */
    public function create($route, array $parameters = array(), $rel = Link::REL_SELF, $type = null)
    {
        $url = call_user_func_array($this->callable, array($route, $parameters));

        return new Link($url, $rel, $type);
    }
}

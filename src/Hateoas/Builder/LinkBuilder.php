<?php

namespace Hateoas\Builder;

use Hateoas\Link;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class LinkBuilder
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param  string $route
     * @param  array  $parameters
     * @param  string $rel
     * @param  string $type
     * @return Link
     */
    public function create($route, array $parameters = array(), $rel = Link::REL_SELF, $type = null)
    {
        $url = $this->router->generate($route, $parameters, true);

        return new Link($url, $rel, $type);
    }
}

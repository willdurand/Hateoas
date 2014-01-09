<?php

namespace Hateoas\Representation\Factory;

use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Pagerfanta\Pagerfanta;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class PagerfantaFactory
{
    /**
     * @var string
     */
    private $pageParameterName;

    /**
     * @var string
     */
    private $limitParameterName;

    public function __construct($pageParameterName = null, $limitParameterName = null)
    {
        $this->pageParameterName  = $pageParameterName;
        $this->limitParameterName = $limitParameterName;
    }

    public function create(Pagerfanta $pager, $route, array $routeParameters = array(), $inline = null, $absolute = false)
    {
        return $this->createV2(
            $pager,
            new Route($route, $routeParameters, $absolute),
            $inline
        );
    }

    public function createV2(Pagerfanta $pager, Route $route, $inline = null)
    {
        if (null === $inline) {
            $inline = new CollectionRepresentation($pager->getCurrentPageResults());
        }

        return new PaginatedRepresentation(
            $inline,
            $route->getName(),
            $route->getParameters(),
            $pager->getCurrentPage(),
            $pager->getMaxPerPage(),
            $pager->getNbPages(),
            $this->pageParameterName,
            $this->limitParameterName,
            $route->isAbsolute()
        );
    }
}

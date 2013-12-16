<?php

namespace Hateoas\Representation\Factory;

use Hateoas\Representation\PaginatedCollection;
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

    public function create(Pagerfanta $pager, $route, array $routeParameters = array(), $inline = null)
    {
        return new PaginatedCollection(
            $inline ?: $pager->getCurrentPageResults(),
            $route,
            $routeParameters,
            $pager->getCurrentPage(),
            $pager->getMaxPerPage(),
            $pager->getNbPages(),
            $this->pageParameterName,
            $this->limitParameterName
        );
    }
}

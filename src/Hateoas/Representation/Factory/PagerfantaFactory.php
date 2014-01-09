<?php

namespace Hateoas\Representation\Factory;

use Hateoas\Configuration\Exclusion;
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

    /**
     * @param Pagerfanta $pager
     * @param string     $route
     * @param array      $routeParameters
     * @param bool       $absolute
     * @param string     $rel
     * @param string     $xmlElementName
     * @param Exclusion  $exclusion
     * @param Exclusion  $embedExclusion
     * @param array      $relations
     *
     * @return PaginatedRepresentation
     */
    public function create(
        Pagerfanta $pager,
        $route,
        array $routeParameters    = array(),
        $absolute                 = false,
        $rel                      = null,
        $xmlElementName           = null,
        Exclusion $exclusion      = null,
        Exclusion $embedExclusion = null,
        array $relations          = array()
    ) {
        $currentPageResults = $pager->getCurrentPageResults();

        if ($currentPageResults instanceof \Traversable) {
            $inline = iterator_to_array($currentPageResults);
        } else {
            $inline = $currentPageResults;
        }

        return new PaginatedRepresentation(
            new CollectionRepresentation($inline, $rel, $xmlElementName, $exclusion, $embedExclusion, $relations),
            $route,
            $routeParameters,
            $pager->getCurrentPage(),
            $pager->getMaxPerPage(),
            $pager->getNbPages(),
            $this->pageParameterName,
            $this->limitParameterName,
            $absolute
        );
    }
}

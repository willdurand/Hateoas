<?php
namespace Hateoas\Representation\Factory;

use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Knp\Component\Pager\Pagination\SlidingPagination;

class KnpPaginatorFactory
{
    /**
     * @var
     */
    private $pageParameterName;

    /**
     * @var
     */
    private $limitParameterName;


    /**
     * @param string $pageParameterName
     * @param string $limitParameterName
     */
    public function __construct($pageParameterName = null, $limitParameterName = null)
    {
        $this->pageParameterName  = $pageParameterName;
        $this->limitParameterName = $limitParameterName;
    }

    /**
     * @param SlidingPagination $slidingPagination
     * @param Route $route
     * @param null $inline
     * @return PaginatedRepresentation
     */
    public function createRepresentation(SlidingPagination $slidingPagination, Route $route, $inline = null)
    {
        if (null === $inline) {
            $inline = new CollectionRepresentation($slidingPagination->getItems());
        }

        return new PaginatedRepresentation(
            $inline,
            $route->getName(),
            $route->getParameters(),
            $slidingPagination->getCurrentPageNumber(),
            $slidingPagination->getItemNumberPerPage(),
            $slidingPagination->getPaginationData()['pageCount'],
            $this->getPageParameterName(),
            $this->getLimitParameterName(),
            $route->isAbsolute(),
            $slidingPagination->getTotalItemCount()
        );

    }

    /**
     * @return string
     */
    public function getPageParameterName()
    {
        return $this->pageParameterName;
    }

    /**
     * @return string
     */
    public function getLimitParameterName()
    {
        return $this->limitParameterName;
    }

}
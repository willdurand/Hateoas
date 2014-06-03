<?php

namespace Hateoas\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("collection")
 *
 * @Hateoas\Relation(
 *      "first",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(1))",
 *          absolute = "expr(object.isAbsolute())"
 *      )
 * )
 * @Hateoas\Relation(
 *      "last",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(object.getPages()))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getPages() === null)"
 *      )
 * )
 * @Hateoas\Relation(
 *      "next",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(object.getPage() + 1))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getPages() !== null && (object.getPage() + 1) > object.getPages())"
 *      )
 * )
 * @Hateoas\Relation(
 *      "previous",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(object.getPage() - 1))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr((object.getPage() - 1) < 1)"
 *      )
 * )
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class PaginatedRepresentation extends RouteAwareRepresentation
{
    /**
     * @var int
     *
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     */
    private $page;

    /**
     * @var int
     *
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     */
    private $limit;

    /**
     * @var int
     *
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     */
    private $pages;

    /**
     * @var int|null
     *
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     */
    private $total;

    /**
     * @var string
     */
    private $pageParameterName;

    /**
     * @var string
     */
    private $limitParameterName;

    public function __construct(
        $inline,
        $route,
        array $parameters        = array(),
        $page,
        $limit,
        $pages,
        $pageParameterName       = null,
        $limitParameterName      = null,
        $absolute                = false,
        $total                   = null
    ) {
        parent::__construct($inline, $route, $parameters, $absolute);

        $this->page               = $page;
        $this->pages              = $pages;
        $this->total              = $total;
        $this->limit              = $limit;
        $this->pageParameterName  = $pageParameterName  ?: 'page';
        $this->limitParameterName = $limitParameterName ?: 'limit';
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param  null  $page
     * @param  null  $limit
     * @return array
     */
    public function getParameters($page = null, $limit = null)
    {
        $parameters = parent::getParameters();

        $parameters[$this->pageParameterName]  = null === $page ? $this->getPage() : $page;
        $parameters[$this->limitParameterName] = null === $limit ? $this->getLimit() : $limit;

        return $parameters;
    }

    /**
     * @return int
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @return int|null
     */
    public function getTotal()
    {
        return $this->total;
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

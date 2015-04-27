<?php

namespace Hateoas\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("collection")
 * @Serializer\AccessorOrder("custom", custom = {"page", "limit", "pages", "total"})
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
class PaginatedRepresentation extends AbstractSegmentedRepresentation
{
    /**
     * @var int
     *
     * @Serializer\Expose
     * @Serializer\Type("integer")
     * @Serializer\XmlAttribute
     */
    private $page;

    /**
     * @var int
     *
     * @Serializer\Expose
     * @Serializer\Type("integer")
     * @Serializer\XmlAttribute
     */
    private $pages;

    /**
     * @var string
     */
    private $pageParameterName;

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
        parent::__construct($inline, $route, $parameters, $limit, $total, $limitParameterName, $absolute);

        $this->page               = $page;
        $this->pages              = $pages;
        $this->pageParameterName  = $pageParameterName  ?: 'page';
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param  null  $page
     * @param  null  $limit
     * @return array
     */
    public function getParameters($page = null, $limit = null)
    {
        $parameters = parent::getParameters($limit);

        unset($parameters[$this->pageParameterName]);
        $parameters[$this->pageParameterName] = null === $page ? $this->getPage() : $page;

        $this->moveParameterToEnd($parameters, $this->getLimitParameterName());

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
     * @return string
     */
    public function getPageParameterName()
    {
        return $this->pageParameterName;
    }
}

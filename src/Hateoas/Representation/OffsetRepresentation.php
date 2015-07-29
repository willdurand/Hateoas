<?php

namespace Hateoas\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("collection")
 * @Serializer\AccessorOrder("custom", custom = {"offset", "limit", "total"})
 *
 * @Hateoas\Relation(
 *      "first",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(0))",
 *          absolute = "expr(object.isAbsolute())"
 *      )
 * )
 * @Hateoas\Relation(
 *      "last",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters((object.getTotal() - 1) - (object.getTotal() - 1) % object.getLimit()))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getTotal() === null)"
 *      )
 * )
 * @Hateoas\Relation(
 *      "next",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(object.getOffset() + object.getLimit()))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getTotal() !== null && (object.getOffset() + object.getLimit()) >= object.getTotal())"
 *      )
 * )
 * @Hateoas\Relation(
 *      "previous",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters((object.getOffset() > object.getLimit()) ? object.getOffset() - object.getLimit() : 0))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(! object.getOffset())"
 *      )
 * )
 *
 * @author Premi Giorgio <giosh94mhz@gmail.com>
 */
class OffsetRepresentation extends AbstractSegmentedRepresentation
{
    /**
     * @var int
     *
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     */
    private $offset;

    /**
     * @var string
     */
    private $offsetParameterName;

    /**
     * @param CollectionRepresentation $inline
     * @param string $route
     * @param integer|null $offset
     * @param integer $limit
     * @param integer $total
     */
    public function __construct(
        $inline,
        $route,
        array $parameters        = array(),
        $offset,
        $limit,
        $total                   = null,
        $offsetParameterName     = null,
        $limitParameterName      = null,
        $absolute                = false
    ) {
        parent::__construct($inline, $route, $parameters, $limit, $total, $limitParameterName, $absolute);

        $this->offset              = $offset;
        $this->offsetParameterName = $offsetParameterName  ?: 'offset';
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param  null  $offset
     * @param  null  $limit
     * @return array
     */
    public function getParameters($offset = null, $limit = null)
    {
        $parameters = parent::getParameters($limit);

        unset($parameters[$this->offsetParameterName]);

        if (null === $offset) {
            $offset = $this->getOffset();
        }

        if ($offset) {
            $parameters[$this->offsetParameterName] = $offset;
            $this->moveParameterToEnd($parameters, $this->getLimitParameterName());
        }

        return $parameters;
    }

    /**
     * @return string
     */
    public function getOffsetParameterName()
    {
        return $this->offsetParameterName;
    }
}

<?php

namespace Hateoas\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Premi Giorgio <giosh94mhz@gmail.com>
 */
abstract class AbstractSegmentedRepresentation extends RouteAwareRepresentation
{
    /**
     * @var int
     *
     * @Serializer\Expose
     * @Serializer\Type("integer")
     * @Serializer\XmlAttribute
     */
    private $limit;

    /**
     * @var int
     *
     * @Serializer\Expose
     * @Serializer\Type("integer")
     * @Serializer\XmlAttribute
     */
    private $total;

    /**
     * @var string
     */
    private $limitParameterName;

    public function __construct(
        $inline,
        $route,
        array $parameters        = array(),
        $limit,
        $total                   = null,
        $limitParameterName      = null,
        $absolute                = false
    ) {
        parent::__construct($inline, $route, $parameters, $absolute);

        $this->total               = $total;
        $this->limit               = $limit;
        $this->limitParameterName  = $limitParameterName ?: 'limit';
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param  null  $limit
     * @return array
     */
    public function getParameters($limit = null)
    {
        $parameters = parent::getParameters();

        $parameters[$this->limitParameterName] = null === $limit ? $this->getLimit() : $limit;

        return $parameters;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return string
     */
    public function getLimitParameterName()
    {
        return $this->limitParameterName;
    }

    /**
     * @param string $key
     */
    protected function moveParameterToEnd(array &$parameters, $key)
    {
        if (! array_key_exists($key, $parameters)) {
            return;
        }

        $value = $parameters[$key];
        unset($parameters[$key]);
        $parameters[$key] = $value;
    }
}

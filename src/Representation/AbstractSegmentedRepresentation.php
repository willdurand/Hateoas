<?php

declare(strict_types=1);

namespace Hateoas\Representation;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
#[Serializer\ExclusionPolicy('all')]
abstract class AbstractSegmentedRepresentation extends RouteAwareRepresentation
{
    /**
     * @Serializer\Expose
     * @Serializer\Type("integer")
     * @Serializer\XmlAttribute
     *
     * @var int
     */
    #[Serializer\Expose]
    #[Serializer\Type('integer')]
    #[Serializer\XmlAttribute]
    private $limit;

    /**
     * @Serializer\Expose
     * @Serializer\Type("integer")
     * @Serializer\XmlAttribute
     *
     * @var int
     */
    #[Serializer\Expose]
    #[Serializer\Type('integer')]
    #[Serializer\XmlAttribute]
    private $total;

    /**
     * @var string
     */
    private $limitParameterName;

    /**
     * @param mixed $inline
     */
    public function __construct(
        $inline,
        string $route,
        array $parameters,
        int $limit,
        ?int $total = null,
        ?string $limitParameterName = null,
        bool $absolute = false
    ) {
        parent::__construct($inline, $route, $parameters, $absolute);

        $this->total               = $total;
        $this->limit               = $limit;
        $this->limitParameterName  = $limitParameterName ?: 'limit';
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param  null  $limit
     */
    public function getParameters(?int $limit = null): array
    {
        $parameters = parent::getParameters();

        $parameters[$this->limitParameterName] = $limit ?? $this->getLimit();

        return $parameters;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function getLimitParameterName(): string
    {
        return $this->limitParameterName;
    }

    protected function moveParameterToEnd(array &$parameters, string $key): void
    {
        if (! array_key_exists($key, $parameters)) {
            return;
        }

        $value = $parameters[$key];
        unset($parameters[$key]);
        $parameters[$key] = $value;
    }
}

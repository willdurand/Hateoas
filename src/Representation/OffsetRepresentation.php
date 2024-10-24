<?php

declare(strict_types=1);

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
 */
#[Serializer\ExclusionPolicy('all')]
#[Serializer\XmlRoot('collection')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['offset', 'limit', 'total'])]
#[Hateoas\Relation(
    'first',
    href: new Hateoas\Route(
        'expr(object.getRoute())',
        parameters: 'expr(object.getParameters(0))',
        absolute: 'expr(object.isAbsolute())',
    ),
)]
#[Hateoas\Relation(
    'last',
    href: new Hateoas\Route(
        'expr(object.getRoute())',
        parameters: 'expr(object.getParameters((object.getTotal() - 1) - (object.getTotal() - 1) % object.getLimit()))',
        absolute: 'expr(object.isAbsolute())',
    ),
    exclusion: new Hateoas\Exclusion(
        excludeIf: 'expr(object.getTotal() === null)',
    )
)]
#[Hateoas\Relation(
    'next',
    href: new Hateoas\Route(
        name: 'expr(object.getRoute())',
        parameters: 'expr(object.getParameters(object.getOffset() + object.getLimit()))',
        absolute: 'expr(object.isAbsolute())'
    ),
    exclusion: new Hateoas\Exclusion(
        excludeIf: 'expr(object.getTotal() !== null && (object.getOffset() + object.getLimit()) >= object.getTotal())',
    ),
)]
#[Hateoas\Relation(
    'previous',
    href: new Hateoas\Route(
        'expr(object.getRoute())',
        parameters: 'expr(object.getParameters((object.getOffset() > object.getLimit()) ? object.getOffset() - object.getLimit() : 0))',
        absolute: 'expr(object.isAbsolute())',
    ),
    exclusion: new Hateoas\Exclusion(
        excludeIf: 'expr(! object.getOffset())',
    ),
)]
class OffsetRepresentation extends AbstractSegmentedRepresentation
{
    /**
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     *
     * @var int
     */
    #[Serializer\Expose]
    #[Serializer\XmlAttribute]
    private $offset;

    /**
     * @var string
     */
    private $offsetParameterName;

    public function __construct(
        CollectionRepresentation $inline,
        string $route,
        array $parameters,
        ?int $offset,
        ?int $limit,
        ?int $total = null,
        ?string $offsetParameterName = null,
        ?string $limitParameterName = null,
        bool $absolute = false
    ) {
        parent::__construct($inline, $route, $parameters, $limit, $total, $limitParameterName, $absolute);

        $this->offset              = $offset;
        $this->offsetParameterName = $offsetParameterName  ?: 'offset';
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @param  null  $offset
     * @param  null  $limit
     */
    public function getParameters(?int $offset = null, ?int $limit = null): array
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

    public function getOffsetParameterName(): string
    {
        return $this->offsetParameterName;
    }
}

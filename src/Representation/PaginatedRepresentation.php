<?php

declare(strict_types=1);

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
 */
#[Serializer\ExclusionPolicy('all')]
#[Serializer\XmlRoot('collection')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['page', 'limit', 'pages', 'total'])]
#[Hateoas\Relation(
    'first',
    href: new Hateoas\Route(
        'expr(object.getRoute())',
        parameters: 'expr(object.getParameters(1))',
        absolute: 'expr(object.isAbsolute())',
    ),
)]
#[Hateoas\Relation(
    'last',
    href: new Hateoas\Route(
        'expr(object.getRoute())',
        parameters: 'expr(object.getParameters(object.getPages()))',
        absolute: 'expr(object.isAbsolute())'
    ),
    exclusion: new Hateoas\Exclusion(
        excludeIf: 'expr(object.getPages() === null)',
    ),
)]
#[Hateoas\Relation(
    'next',
    href: new Hateoas\Route(
        'expr(object.getRoute())',
        parameters: 'expr(object.getParameters(object.getPage() + 1))',
        absolute: 'expr(object.isAbsolute())',
    ),
    exclusion: new Hateoas\Exclusion(
        excludeIf: 'expr(object.getPages() !== null && (object.getPage() + 1) > object.getPages())',
    ),
)]
#[Hateoas\Relation(
    'previous',
    href: new Hateoas\Route(
        'expr(object.getRoute())',
        parameters: 'expr(object.getParameters(object.getPage() - 1))',
        absolute: 'expr(object.isAbsolute())',
    ),
    exclusion: new Hateoas\Exclusion(
        excludeIf: 'expr((object.getPage() - 1) < 1)',
    ),
)]
class PaginatedRepresentation extends AbstractSegmentedRepresentation
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
    private $page;

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
    private $pages;

    /**
     * @var string
     */
    private $pageParameterName;

    /**
     * @param mixed $inline
     */
    public function __construct(
        $inline,
        string $route,
        array $parameters,
        ?int $page,
        ?int $limit,
        ?int $pages,
        ?string $pageParameterName = null,
        ?string $limitParameterName = null,
        bool $absolute = false,
        ?int $total = null
    ) {
        parent::__construct($inline, $route, $parameters, $limit, $total, $limitParameterName, $absolute);

        $this->page               = $page;
        $this->pages              = $pages;
        $this->pageParameterName  = $pageParameterName  ?: 'page';
    }

    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param  null  $page
     * @param  null  $limit
     */
    public function getParameters($page = null, $limit = null): array
    {
        $parameters = parent::getParameters($limit);

        unset($parameters[$this->pageParameterName]);
        $parameters[$this->pageParameterName] = $page ?? $this->getPage();

        $this->moveParameterToEnd($parameters, $this->getLimitParameterName());

        return $parameters;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function getPageParameterName(): string
    {
        return $this->pageParameterName;
    }
}

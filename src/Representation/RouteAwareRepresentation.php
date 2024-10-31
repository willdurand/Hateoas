<?php

declare(strict_types=1);

namespace Hateoas\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters())",
 *          absolute = "expr(object.isAbsolute())"
 *      )
 * )
 */
#[Serializer\ExclusionPolicy('all')]
#[Hateoas\Relation(
    'self',
    href: new Hateoas\Route(
        'expr(object.getRoute())',
        parameters: 'expr(object.getParameters())',
        absolute: 'expr(object.isAbsolute())',
    ),
)]
class RouteAwareRepresentation
{
    /**
     * @Serializer\Inline
     * @Serializer\Expose
     *
     * @var mixed
     */
    #[Serializer\Inline]
    #[Serializer\Expose]
    private $inline;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var bool
     */
    private $absolute;

    /**
     * @param mixed $inline
     */
    public function __construct($inline, string $route, array $parameters = [], bool $absolute = false)
    {
        $this->inline     = $inline;
        $this->route      = $route;
        $this->parameters = $parameters;
        $this->absolute   = $absolute;
    }

    /**
     * @return mixed
     */
    public function getInline()
    {
        return $this->inline;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function isAbsolute(): bool
    {
        return $this->absolute;
    }
}

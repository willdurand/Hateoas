<?php

declare(strict_types=1);

namespace Hateoas\Configuration;

use JMS\Serializer\Expression\Expression;

class Relation
{
    /**
     * The link "rel" attribute
     *
     * @var string
     */
    private $name;

    /**
     * @var string|Route|Expression|null
     */
    private $href;

    /**
     * @var string[]|Expression[]
     */
    private $attributes;

    /**
     * @var Embedded|null
     */
    private $embedded;

    /**
     * @var Exclusion|null
     */
    private $exclusion;

    /**
     * @param string|Expression $name
     * @param string|Route          $href
     * @param Embedded|string|mixed $embedded
     */
    public function __construct(string $name, $href = null, $embedded = null, array $attributes = [], ?Exclusion $exclusion = null)
    {
        if (null !== $embedded && !$embedded instanceof Embedded) {
            $embedded = new Embedded($embedded);
        }

        if (null === !$href && null === $embedded) {
            throw new \InvalidArgumentException('$href and $embedded cannot be both null.');
        }

        $this->name       = $name;
        $this->href       = $href;
        $this->embedded   = $embedded;
        $this->attributes = $attributes;
        $this->exclusion  = $exclusion;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Route|string|null
     */
    public function getHref()
    {
        return $this->href;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getEmbedded(): ?Embedded
    {
        return $this->embedded;
    }

    public function getExclusion(): ?Exclusion
    {
        return $this->exclusion;
    }
}

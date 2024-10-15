<?php

declare(strict_types=1);

namespace Hateoas\Model;

class Link
{
    /**
     * @var string
     */
    private $rel;

    /**
     * @var string
     */
    private $href;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(string $rel, string $href, array $attributes = [])
    {
        $this->rel        = $rel;
        $this->href       = $href;
        $this->attributes = $attributes;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getRel(): string
    {
        return $this->rel;
    }
}

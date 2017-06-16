<?php

namespace Hateoas\Model;

use Psr\Link\EvolvableLinkInterface;
use Psr\Link\LinkInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Link implements LinkInterface, EvolvableLinkInterface
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

    /**
     * @param string $rel
     * @param string $href
     * @param array  $attributes
     */
    public function __construct($rel, $href, array $attributes = array())
    {
        $this->rel        = $rel;
        $this->href       = $href;
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * {@inheritDoc}
     */
    public function isTemplated()
    {
        return array_key_exists('templated', $this->attributes) && $this->attributes['templated'];
    }

    /**
     * {@inheritDoc}
     */
    public function getRels()
    {
        return [
            $this->rel,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function withHref($href)
    {
        return new static($this->rel, $href, $this->attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function withRel($rel)
    {
        return new static($rel, $this->href, $this->attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function withoutRel($rel)
    {
        // What should we do here?
        throw new \Exception('Not implemented');
    }

    /**
     * {@inheritDoc}
     */
    public function withAttribute($attribute, $value)
    {
        if (array_key_exists($attribute, $this->attributes) && $this->attributes[$attribute] === $value) {
            return $this;
        }
        return new static($this->rel, $this->href, array_merge($this->attributes, [
            $attribute => $value,
        ]));
    }

    /**
     * {@inheritDoc}
     */
    public function withoutAttribute($attribute)
    {
        if (!array_key_exists($attribute, $this->attributes)) {
            return $this;
        }

        $attributes = array_filter($this->attributes, function ($filteredAttribute) use ($attribute) {
            return $filteredAttribute !== $attribute;
        }, ARRAY_FILTER_USE_KEY);
        return new static($this->rel, $this->href, $attributes);
    }
}

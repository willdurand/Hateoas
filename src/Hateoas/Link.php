<?php

namespace Hateoas;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Link
{
    const REL_SELF      = 'self';
    const REL_FIRST     = 'first';
    const REL_LAST      = 'last';
    const REL_PREVIOUS  = 'previous';
    const REL_NEXT      = 'next';
    const REL_PARENT    = 'parent';

    /**
     * @var string
     */
    private $href;

    /**
     * @var string
     */
    private $rel;

    /**
     * @var string
     */
    private $type;

    public function __construct($href, $rel, $type = null)
    {
        $this->href = $href;
        $this->rel  = $rel;
        $this->type = $type;
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}

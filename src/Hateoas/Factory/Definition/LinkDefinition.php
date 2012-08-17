<?php

namespace Hateoas\Factory\Definition;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class LinkDefinition
{
    /**
     * @var string
     */
    private $rel;

    /**
     * @var string
     */
    private $type;

    public function __construct($rel, $type = null)
    {
        $this->rel  = $rel;
        $this->type = $type;
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

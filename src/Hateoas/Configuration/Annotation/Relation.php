<?php

namespace Hateoas\Configuration\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
final class Relation
{
    /**
     * @Required
     *
     * @var string
     */
    public $name;

    /**
     * @Required
     */
    public $href;

    public $embed = null;

    /**
     * @var array
     */
    public $attributes = array();
}

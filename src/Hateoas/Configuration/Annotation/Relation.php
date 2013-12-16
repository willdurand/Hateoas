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

    public $href = null;

    public $embedded = null;

    /**
     * @var array
     */
    public $attributes = array();

    /**
     * @var \Hateoas\Configuration\Annotation\Exclusion
     */
    public $exclusion = null;
}

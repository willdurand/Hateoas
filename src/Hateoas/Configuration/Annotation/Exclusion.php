<?php

namespace Hateoas\Configuration\Annotation;

/**
 * @Annotation
 * @Target("ANNOTATION")
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
final class Exclusion
{
    /**
     * @var array
     */
    public $groups = null;

    /**
     * @var mixed float/integer
     */
    public $sinceVersion = null;

    /**
     * @var mixed float/integer
     */
    public $untilVersion = null;

    /**
     * @var int
     */
    public $maxDepth = null;

    /**
     * @var string
     */
    public $excludeIf = null;
}

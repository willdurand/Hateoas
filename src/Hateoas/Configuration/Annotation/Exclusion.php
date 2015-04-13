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
     * If you are working with the FOS/Rest Bundle, be reminded to set
     * Rest\View(serializerEnableMaxDepthChecks=true)
     * on your controller
     *
     * @var int
     */
    public $maxDepth = null;

    /**
     * @var string
     */
    public $excludeIf = null;
}

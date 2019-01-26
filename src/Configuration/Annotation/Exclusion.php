<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Annotation;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
final class Exclusion
{
    /**
     * @var array
     */
    public $groups = null;

    /**
     * @var string
     */
    public $sinceVersion = null;

    /**
     * @var string
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

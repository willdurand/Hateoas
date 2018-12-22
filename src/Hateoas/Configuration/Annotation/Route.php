<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Annotation;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
class Route
{
    /**
     * @Required
     * @var string
     */
    public $name;

    /**
     * @Required
     * @var mixed
     */
    public $parameters = [];

    /**
     * @var mixed
     */
    public $absolute = false;

    /**
     * @var string
     */
    public $generator = null;
}

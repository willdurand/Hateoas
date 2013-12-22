<?php

namespace Hateoas\Configuration\Annotation;

/**
 * @Annotation
 * @Target("ANNOTATION")
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Route
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
    public $parameters = array();

    public $absolute = false;

    public $generator = null;
}

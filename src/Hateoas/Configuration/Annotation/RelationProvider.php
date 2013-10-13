<?php

namespace Hateoas\Configuration\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class RelationProvider
{
    /**
     * @var string
     */
    public $name;
}

<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class RelationProvider
{
    /**
     * @var string
     */
    public $name;
}

<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Relation
{
    /**
     * @Required
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $href = null;

    /**
     * @var mixed
     */
    public $embedded = null;

    /**
     * @var array
     */
    public $attributes = [];

    /**
     * phpcs:disable
     * @var \Hateoas\Configuration\Annotation\Exclusion
     * phpcs:enable
     */
    public $exclusion = null;
}

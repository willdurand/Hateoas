<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Annotation;

use JMS\Serializer\Annotation\AnnotationUtilsTrait;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Relation
{
    use AnnotationUtilsTrait;

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

    /**
     * @param array|string|null $values
     * @param string|Route $href
     * @param string|Embedded $embedded
     */
    public function __construct($values = [], ?string $name = null, $href = null, $embedded = null, array $attributes = [], ?Exclusion $exclusion = null)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}

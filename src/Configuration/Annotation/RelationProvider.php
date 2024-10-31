<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Annotation;

use JMS\Serializer\Annotation\AnnotationUtilsTrait;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class RelationProvider
{
    use AnnotationUtilsTrait;

    /**
     * @var string
     */
    public $name;

    /**
     * @param array|string|null $values
     */
    public function __construct($values = [], ?string $name = null)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
